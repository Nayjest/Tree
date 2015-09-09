<?php
namespace Nayjest\Tree;

use Nayjest\Collection\Extended\ObjectCollection;

/**
 * Interface ChildNodeInterface
 *
 * Interface of terminal node in the tree data structure.
 *
 */
interface ChildNodeInterface
{
    /**
     * Attaches component to parent.
     *
     * @internal
     *
     * @param ParentNodeInterface $parent
     */
    public function internalSetParent(ParentNodeInterface $parent);

    /**
     * @internal
     */
    public function internalUnsetParent();

    /**
     * Returns parent object.
     *
     * @return ParentNodeInterface|null
     */
    public function parent();

    /**
     * Detaches node from parent.
     *
     * @return $this
     */
    public function detach();

    /**
     * Attaches node to parent.
     *
     * @param ParentNodeInterface $parent
     *
     * @return $this
     */
    public function attachTo(ParentNodeInterface $parent);

    /**
     * @return ObjectCollection
     */
    public function parents();
}
