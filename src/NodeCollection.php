<?php

namespace Nayjest\Tree;

use InvalidArgumentException;
use Nayjest\Collection\Extended\ObjectCollection;
use Nayjest\Tree\Exception\LockedNodeException;

/**
 * Class NodeCollection.
 *
 * NodeCollection in addition to basic collection facilities
 * manages parent-child relationships and guarantees tree structure integrity.
 */
class NodeCollection extends ObjectCollection
{
    /**
     * @var ParentNodeInterface
     */
    protected $parentNode;

    public function __construct(
        ParentNodeInterface $parentNode,
        array $nodes = null
    ) {
        $this->parentNode = $parentNode;
        parent::__construct($nodes);
    }

    /**
     * Adds component to collection.
     *
     * If component is already in collection, it will not be added twice.
     *
     * @param ChildNodeInterface $item
     * @param bool               $prepend Pass true to add component to the beginning of an array.
     *
     * @return $this
     */
    public function add($item, $prepend = false)
    {
        if (!$item instanceof ChildNodeInterface) {
            $details = is_object($item) ? get_class($item) : gettype($item);
            throw new InvalidArgumentException(
                "NodeCollection accepts only objects implementing ChildNodeInterface, $details given."
            );
        }
        $old = $item->parent();
        if ($old === $this->parentNode) {
            return $this;
        } elseif ($old !== null) {
            $item->detach();
        }
        $this->checkUnlocked($item);
        parent::add($item, $prepend);
        $item->internalSetParent($this->parentNode);

        return $this;
    }

    /**
     * @param ChildNodeInterface $item
     *
     * @return $this
     */
    public function remove($item)
    {
        if ($item->parent() === $this->parentNode) {
            $this->checkUnlocked($item);
            $item->internalUnsetParent();
            parent::remove($item);
        }

        return $this;
    }

    public function clear()
    {
        /** @var ChildNodeInterface $item */
        foreach ($this->items() as $item) {
            $this->checkUnlocked($item);
            $item->internalUnsetParent();
        }

        return parent::clear();
    }

    protected function createCollection(array $items)
    {
        return new ObjectCollection($items);
    }

    private function checkUnlocked(ChildNodeInterface $child)
    {
        if ($child->isLocked()) {
            throw new LockedNodeException();
        }
    }
}
