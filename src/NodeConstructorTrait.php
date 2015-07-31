<?php

namespace Nayjest\Tree;

trait NodeConstructorTrait
{
    abstract protected function initializeCollection(array $items);

    public function __construct(
        ParentNodeInterface $parent = null,
        array $children = null
    )
    {
        if ($children !== null) {
            $this->initializeCollection($children);
        }
        if ($parent !== null) {
            $parent->children()->add($this);
        }
    }
}
