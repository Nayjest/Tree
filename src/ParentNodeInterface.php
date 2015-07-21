<?php
namespace Nayjest\Tree;
use Nayjest\Collection\CollectionInterface;
use Nayjest\Collection\CollectionReadInterface;

/**
 * Interface ParentNodeInterface
 *
 * Interface of parent node in the tree data structure.
 *
 */
interface ParentNodeInterface
{
    /**
     * Returns children collection.
     *
     * @return CollectionInterface|CollectionReadInterface
     */
    public function children();

    public function isWritable();
}
