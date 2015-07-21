<?php
namespace Nayjest\Tree;

/**
 * Class ParentNodeTrait
 *
 * @implements ParentNodeInterface
 */
trait ParentNodeTrait
{
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

    protected function initializeCollection(array $items)
    {
        /** @var ParentNodeInterface|ParentNodeTrait $this */
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

    final public function isWritable()
    {
        return $this->children()->isWritable();
    }
}
