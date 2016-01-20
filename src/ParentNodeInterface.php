<?php

namespace Nayjest\Tree;

use Nayjest\Collection\CollectionInterface;
use Nayjest\Collection\CollectionReadInterface;
use Nayjest\Collection\Extended\ObjectCollection;
use Traversable;

/**
 * Interface ParentNodeInterface.
 *
 * Interface of parent node in the tree data structure.
 */
interface ParentNodeInterface
{
    /**
     * Returns children collection (writable or readonly).
     *
     * @return CollectionInterface|CollectionReadInterface
     */
    public function children();

    /**
     * Returns true if children collection is writable, returns false otherwise.
     *
     * @return bool
     */
    public function isWritable();

    /**
     * Returns all descendant nodes.
     *
     * @return CollectionInterface|ObjectCollection
     */
    public function getChildrenRecursive();

    /**
     * Attaches child nodes. If current node has another children, they will be replaced.
     * @param array|Traversable $children
     *
     * @return $this
     */
    public function setChildren($children);

    /**
     * Attaches child node.
     *
     * @param ChildNodeInterface $item
     * @return $this
     */
    public function addChild(ChildNodeInterface $item);

    /**
     * Attaches list of child nodes.
     *
     * @param array|Traversable $children
     *
     * @return $this
     */
    public function addChildren($children);
}
