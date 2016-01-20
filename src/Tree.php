<?php

namespace Nayjest\Tree;

use Nayjest\Collection\Extended\Registry;
use Nayjest\Collection\Extended\RegistryInterface;
use Nayjest\Tree\Exception\NodeNotFoundException;
use Nayjest\Tree\Utils\TreeBuilder;

/**
 * Class for working with tree.
 *
 * ==== [ RU ] ====
 * Класс для работы с деревом.
 *
 * Особенности реализации:
 *  (1) Раздельно хранит реестр именованных узлов (1-a) и конфигурацию дерева (1-b)
 *  (2) Замораживает связи между узлами, описанными в конфигурации
 * Фичи:
 *   1-a.1 Доступ к узлам дерева по имени [ $tree->nodes()->get('name') ]
 *         Решает задачи:
 *           * Доступ к функциональным(именованным) узлам дерева
 *         Альтернативы:
 *           * Pекурсивный поиск по свойству
 *             + есть из коробки
 *             - плохая производительность
 *           * Хранение ссылок на узлы (возможно заслуживает внимания)
 *             - нужно реализовать обновление ссылок при замене узлов
 *         Cons/Pros
 *         + Быстрее рекурсивного поиска
 *         + Поиск ограничивается реестром, что исключает конфликты имен с элементами нижележащих деревьев
 *         - сложнее реализация (рекурсивный поиск есть из коробки)
 *   1-a.2 Можно определить функциональную принадлежность узла конкретному дереву
 *   1-b.1 Позволяет заменить функциональный узел с сохранением функционвльных потомков
 *   2. Выборочное замораживание структуры дерева
 *      Решает проблему:
 *       Поддержка целосности структуры, однозначность операций т. к. открыто 2 API для модификации дерева
 *       ситуации:
 *         * узел удален из дерева через node api, но остался в $hierarchy: после $tree->update() он опять туда попадет
 *         * через api узлов можно сломать структуру, ожидаемую от дерева, т. е. нельзя завязаться на уонкретное дерево
 *      Альтернативы:
 *          * Сокрытие дочерних элементов
 *            - нельзя вообще никак модифицировать/читать структуру
 *          * Readonly root.children
 *            - не решает проблему
 *          * Readonly children of all nodes in registry
 *            - не проще в реализации, но налагает излишние ограничения
 *      Cons/Pros
 *        + позволяет свободно модифицировать узлы, не считая связей, определенных деревом
 *        + Через API узлов нельзя сломать структуру дерева
 */
class Tree
{
    const NODE_EXISTS = 1;
    const NODE_ADDED = 2;
    const PARENT_NODE_NOT_FOUND = 3;

    /**
     * @var array
     */
    private $hierarchy;

    /**
     * @var NodeInterface[]|RegistryInterface
     */
    private $nodes;

    /**
     * @var TreeBuilder
     */
    private $builder;

    /**
     * @var bool
     */
    private $updateRequired = true;

    /**
     * @var ParentNodeInterface
     */
    private $root;

    /**
     * Tree constructor.
     *
     * @param ParentNodeInterface $root
     * @param array               $hierarchy
     * @param array|Registry      $nodes
     */
    public function __construct(
        ParentNodeInterface $root,
        array $hierarchy,
        $nodes
    ) {
        $this->hierarchy = $hierarchy;
        $this->nodes = $nodes instanceof Registry ? $nodes : new Registry($nodes);
        $this->builder = Utils::getDefaultTreeBuilder();
        $this->root = $root;
    }

    /**
     * @return ParentNodeInterface
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @return NodeInterface[]
     */
    public function getNodes()
    {
        return $this->nodes->toArray();
    }

    /**
     * @return array
     */
    public function getHierarchy()
    {
        return $this->hierarchy;
    }

    /**
     * Builds tree based on its nodes registry and hierarchy configuration
     * if structure update required.
     */
    public function build()
    {
        if ($this->updateRequired) {
            $this->updateRequired = false;
            foreach ($this->nodes as $node) {
                if ($node->parent()) {
                    $node->unlock()->detach();
                }
            }
            $this->root->addChildren($this->builder->build($this->hierarchy, $this->nodes->toArray()));
            foreach ($this->nodes as $node) {
                if ($node->parent() === $this->root || $this->nodes->contains($node->parent())) {
                    $node->lock();
                }
            }
        }
    }

    /**
     * @param string|null $parentName
     * @param string $nodeName
     * @param ChildNodeInterface $node
     * @return Tree
     */
    public function append($parentName = null, $nodeName, ChildNodeInterface $node)
    {
        return $this->add($parentName, $nodeName, $node, false);
    }

    /**
     * @param string|null $parentName
     * @param string $nodeName
     * @param ChildNodeInterface $node
     * @return Tree
     */
    public function prepend($parentName = null, $nodeName, ChildNodeInterface $node)
    {
        return $this->add($parentName, $nodeName, $node, true);
    }

    /**
     * Replaces named tree node to new one.
     *
     * @param string             $nodeName
     * @param ChildNodeInterface $node
     *
     * @return $this
     */
    public function replace($nodeName, ChildNodeInterface $node)
    {
        $this->removeNodeFromList($nodeName);
        $this->nodes->set($nodeName, $node);
        $this->updateRequired = true;

        return $this;
    }

    /**
     * Adds multiple nodes to tree.
     *
     * @param string|null $parentName root node will be used if null
     * @param array $namedItems array with nodes where keys are node names
     * @param bool $prepend if true, nodes will be prepended, otherwise appended to parent
     * @return $this
     */
    public function addMany($parentName, array $namedItems, $prepend = false)
    {
        foreach ($namedItems as $name => $item) {
            $this->add($parentName, $name, $item, $prepend);
        }

        return $this;
    }

    /**
     * Finds node by its name.
     *
     * @param string $nodeName
     * @return null|object
     */
    public function get($nodeName)
    {
        return $this->nodes->get($nodeName);
    }

    /**
     * Returns true if tree contains node with specified name, returns false otherwise.
     *
     * @param string $nodeName
     * @return bool
     */
    public function has($nodeName)
    {
        return $this->nodes->hasKey($nodeName);
    }

    /**
     * Moves node to another parent.
     *
     * @param string $nodeName target node name
     * @param string|null $newParentName parent node name;  root will be used if null
     * @param bool $prepend
     * @return $this
     */
    public function move($nodeName, $newParentName, $prepend = false)
    {
        if (!$this->nodes->hasKey($nodeName)) {
            throw new NodeNotFoundException();
        }
        $node = $this->nodes->get($nodeName);
        $this->remove($nodeName);
        $this->add($newParentName, $nodeName, $node, $prepend);

        return $this;
    }

    /**
     * Removes node by its name.
     *
     * @param string $nodeName
     *
     * @return $this
     */
    public function remove($nodeName)
    {
        $children = self::removeTreeNode($this->hierarchy, $nodeName);
        // @todo remove all children
        $this->removeNodeFromList($nodeName);
        $this->updateRequired = true;

        return $this;
    }

    /**
     * Adds new tree node. If node exists, replaces it.
     *
     * @param string|null        $parentName root if null
     * @param string             $nodeName   new node name
     * @param ChildNodeInterface $node
     *
     * @return $this
     */
    protected function add($parentName = null, $nodeName, ChildNodeInterface $node, $prepend = false)
    {
        if (!self::addTreeNode($this->hierarchy, $parentName, $nodeName, $prepend)) {
            throw new NodeNotFoundException(
                "Can't add '$nodeName' node to '$parentName': '$parentName' node not found."
            );
        }
        $this->removeNodeFromList($nodeName);
        $this->nodes->set($nodeName, $node);
        $this->updateRequired = true;

        return $this;
    }

    /**
     * @todo try array_walk_recursive
     *
     * @param array  $tree
     * @param string $parent node name or null for inserting into root node
     * @param $node
     *
     * @throws \Exception
     *
     * @return bool false if no parent found
     */
    private static function addTreeNode(array &$tree, $parent, $node, $prepend = false)
    {
        if ($parent === null) {
            if (array_key_exists($node, $tree)) {
                throw new \Exception('Node already exists');
            }
            if ($prepend) {
                $tree = array_merge([$node => []], $tree);
            } else {
                $tree[$node] = [];
            }

            return true;
        }
        foreach ($tree as $key => &$value) {
            if ($key === $parent) {
                return self::addTreeNode($value, null, $node, $prepend);
            } else {
                if (self::addTreeNode($value, $parent, $node, $prepend)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param array $config
     * @param $node
     *
     * @return false|array children of deleted node
     */
    private static function removeTreeNode(array &$config, $node)
    {
        foreach ($config as $key => &$value) {
            if ($key === $node) {
                $children = $config[$node];
                unset($config[$node]);

                return $children;
            } else {
                $result = self::removeTreeNode($value, $node);
                if ($result !== false) {
                    return $result;
                }
            }
        }

        return false;
    }

    protected function removeNodeFromList($nodeName)
    {
        if ($this->nodes->hasKey($nodeName)) {
            $oldNode = $this->nodes->get($nodeName);
            if ($oldNode->parent()) {
                $oldNode->unlock()->detach();
            }
            $this->nodes->removeByKey($nodeName);
            $this->updateRequired = true;
        }
    }
}
