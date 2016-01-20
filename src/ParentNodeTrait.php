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
     * @var NodeCollection
     */
    protected $collection;

    /**
     * Returns default child components.
     *
     * Override this method if you need.
     *
     * @return ChildNodeInterface[]
     */
    protected function defaultChildren()
    {
        return [];
    }

    /**
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
     * Returns child components.
     *
     * @return \Nayjest\Collection\CollectionInterface
     */
    public function children()
    {
        if ($this->collection === null) {
            $this->initializeCollection($this->defaultChildren());
        }

        return $this->collection;
    }

    /**
     * @return bool
     */
    final public function isWritable()
    {
        return $this->children()->isWritable();
    }

    /**
     * @return ObjectCollection
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
     * @param array|Traversable $children
     *
     * @return $this
     */
    public function setChildren($children)
    {
        $this->children()->set($children);

        return $this;
    }

    /**
     * @param $item
     *
     * @return $this
     */
    public function addChild(ChildNodeInterface $item)
    {
        $this->children()->add($item);

        return $this;
    }

    /**
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
