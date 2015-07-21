<?php

namespace Nayjest\Tree;

use Nayjest\Collection\Decorator\ReadonlyCollection;

trait ReadonlyParentNodeTrait
{
    use ParentNodeTrait {
        ParentNodeTrait::initializeCollection as private initializeWritableCollection;
    };

    protected function initializeCollection(array $items)
    {
        $this->initializeWritableCollection($items);
        $this->collection = new ReadonlyCollection($this->collection);
    }
}
