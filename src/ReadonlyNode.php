<?php

namespace Nayjest\Tree;

class ReadonlyNode implements NodeInterface
{
    use ReadonlyNodeTrait;

    /**
     * Node constructor.
     * @param ParentNodeInterface|null $parent
     * @param array|null $children
     */
    public function __construct(
        ParentNodeInterface $parent = null,
        array $children = null
    )
    {
        ($children !== null) && $this->initializeCollection($children);
        $parent && $parent->children()->add($this);
    }
}
