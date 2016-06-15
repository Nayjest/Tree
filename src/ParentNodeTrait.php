<?php

namespace Nayjest\Tree;

use Nayjest\Collection\Extended\ObjectCollection;
use Traversable;

/**
 * Trait ParentNodeTrait.
 *
 * @implements ParentNodeInterface
 * @see ParentNodeInterface
 */
trait ParentNodeTrait
{
    /**
     * Collection of child nodes (objects implementing ChildNodeInterface)
     * @var NodeCollection
     */
    protected $collection;

    /**
     * Returns array containing default child nodes.
     *
     * Override this method if you need to implement parent node class that must contain certain children by default.
     *
     * @return ChildNodeInterface[]
     */
    protected function defaultChildren()
    {
        return [];
    }

    /**
     * Initializes collection of child nodes.
     * 
     * This method is called once when accessing collection first time
     * or constructing node with children passed to constructor argument.
     * 
     * @param array $items
     */
    protected function initializeCollection(array $items)
    {
        /* @var ParentNodeInterface|ParentNodeTrait $this */
        $this->collection = new NodeCollection(
            $this,
            $items
        );
    }

    /**
     * Returns collection of child nodes.
     *
     * @return \Nayjest\Collection\CollectionInterface|ChildNodeInterface[]
     */
    public function children()
    {
        if ($this->collection === null) {
            $this->initializeCollection($this->defaultChildren());
        }

        return $this->collection;
    }

    /**
     * Returns true if collection of child nodes is writable (open for modifications), returns false otherwise.
     * 
     * @return bool
     */
    final public function isWritable()
    {
        return $this->children()->isWritable();
    }

    /**
     * Returns collection containing all descendant nodes.
     * 
     * @return CollectionInterface|ObjectCollection|ChildNodeInterface[]
     */
    public function getChildrenRecursive()
    {
        $res = new ObjectCollection();
        foreach ($this->children() as $child) {
            $res->add($child);
            if ($child instanceof ParentNodeInterface) {
                $res->addMany($child->getChildrenRecursive());
            }
        }

        return $res;
    }

    /**
     * Clears collection of child nodes and attaches new children.
     * 
     * @param Traversable|ChildNodeInterface[] $children
     *
     * @return $this
     */
    public function setChildren($children)
    {
        $this->children()->set($children);

        return $this;
    }

    /**
     * Attaches child node.
     * 
     * @param ChildNodeInterface $item
     *
     * @return $this
     */
    public function addChild(ChildNodeInterface $item)
    {
        $this->children()->add($item);

        return $this;
    }

    /**
     * Attaches list of child nodes.
     * 
     * @param array|Traversable $children
     *
     * @return $this
     */
    public function addChildren($children)
    {
        $this->children()->addMany($children);

        return $this;
    }
}
