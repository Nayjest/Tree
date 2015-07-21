<?php
namespace Nayjest\Tree;

use RuntimeException;

/**
 * Class ChildNodeTrait
 *
 * @implements ChildNodeInterface
 *
 */
trait ChildNodeTrait
{
    /**
     * @internal
     * @var ParentNodeInterface|ChildNodeInterface
     * */
    private $parentNode;

    /**
     * Attaches component to registry.
     *
     * @param ParentNodeInterface $parent
     * @return null
     */
    final public function internalSetParent(ParentNodeInterface $parent)
    {
        $this->parentNode = $parent;
    }

    final public function internalUnsetParent()
    {
        $this->parentNode = null;
    }

    /**
     * Returns parent node.
     *
     * @return ParentNodeInterface|null
     */
    final public function parent()
    {
        return $this->parentNode;
    }

    final public function detach()
    {
        self::checkWritableParent($this->parentNode);
        $this->parentNode->children()->remove($this);
        return $this;
    }

    final public function attachTo(ParentNodeInterface $parent)
    {
        self::checkWritableParent($parent);
        $parent->children()->addItem($this);
        return $this;
    }

    private static function checkWritableParent(ParentNodeInterface $parent = null)
    {
        if ($parent === null) {
            throw new RuntimeException(
                'Trying to detach node that\'s not attached to parent.'
            );
        }
        if (!$parent->children()->isWritable()) {
            throw new RuntimeException(
                'Trying to detach node from immutable root'
            );
        }
    }
}
