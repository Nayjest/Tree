<?php
namespace Nayjest\Tree;

use Nayjest\Collection\CollectionInterface;
use Nayjest\Collection\CollectionReadInterface;
use Nayjest\Collection\Extended\ObjectCollection;

/**
 * Interface ParentNodeInterface
 *
 * Interface of parent node in the tree data structure.
 *
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
     * Returns true in children collection is writable.
     *
     * @return bool
     */
    public function isWritable();

    /**
     * @return CollectionInterface|ObjectCollection
     */
    public function getChildrenRecursive();
}
