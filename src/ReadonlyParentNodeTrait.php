<?php

namespace Nayjest\Tree;

use Nayjest\Collection\CollectionReadInterface;
use Nayjest\Collection\Decorator\ReadonlyCollection;

trait ReadonlyParentNodeTrait
{
    use ParentNodeTrait {
        ParentNodeTrait::children as private writableChildren;
    }

    private $readonlyCollection;

    /**
     * Returns child components.
     *
     * @return CollectionReadInterface
     */
    public function children()
    {
        if ($this->readonlyCollection === null) {
            $this->readonlyCollection = new ReadonlyCollection(
                $this->writableChildren()
            );
        }
        return $this->readonlyCollection;
    }
}
