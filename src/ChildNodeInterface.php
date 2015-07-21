<?php
namespace Nayjest\Tree;

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

    public function detach();

    public function attachTo(ParentNodeInterface $parent);
}
