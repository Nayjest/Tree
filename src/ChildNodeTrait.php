<?php

namespace Nayjest\Tree;

use Evenement\EventEmitterTrait;
use Nayjest\Collection\Extended\ObjectCollection;
use Nayjest\Tree\Exception\LockedNodeException;
use Nayjest\Tree\Exception\NoParentException;
use Nayjest\Tree\Exception\ReadonlyNodeModifyException;

/**
 * Trait ChildNodeTrait.
 *
 * @implements ChildNodeInterface
 * @see ChildNodeInterface
 */
trait ChildNodeTrait
{
    use EventEmitterTrait;

    /**
     * @internal
     *
     * @var ParentNodeInterface|ChildNodeInterface
     * */
    private $parentNode;

    private $locked = false;

    /**
     * Attaches component to registry.
     *
     * @param ParentNodeInterface $parent
     *
     * @return null
     */
    final public function internalSetParent(ParentNodeInterface $parent)
    {
        $this->emit('parent.change', [$parent, $this]);
        $this->parentNode = $parent;
    }

    final public function internalUnsetParent()
    {
        $this->emit('parent.change', [null, $this]);
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

    /**
     * @return ObjectCollection
     */
    public function parents()
    {
        $parents = new ObjectCollection();
        $current = $this->parent();
        while ($current instanceof ParentNodeInterface) {
            $parents->add($current);
            if (!$current instanceof ChildNodeInterface) {
                break;
            }
            $current = $current->parent();
        }

        return $parents;
    }

    final public function detach()
    {
        $this->checkParentRelation($this->parentNode);
        $this->parentNode->children()->remove($this);

        return $this;
    }

    final public function attachTo(ParentNodeInterface $parent)
    {
        $this->checkParentRelation($parent);
        $parent->children()->add($this);

        return $this;
    }

    public function onParentChange(callable $callback, $once = false)
    {
        if ($once) {
            $this->once('parent.change', $callback);
        } else {
            $this->on('parent.change', $callback);
        }

        return $this;
    }

    private function checkParentRelation(ParentNodeInterface $parent = null)
    {
        if ($parent === null) {
            throw new NoParentException();
        }
        if (!$parent->isWritable()) {
            throw new ReadonlyNodeModifyException();
        }
        if ($this->isLocked()) {
            throw new LockedNodeException();
        }
    }

    public function lock()
    {
        $this->locked = true;

        return $this;
    }

    public function unlock()
    {
        $this->locked = false;

        return $this;
    }

    public function isLocked()
    {
        return $this->locked;
    }
}
