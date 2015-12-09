<?php

namespace Nayjest\Tree;

use Nayjest\Collection\CollectionReadInterface;
use Nayjest\Collection\Decorator\ReadonlyObjectCollection;

trait ReadonlyParentNodeTrait
{
    use ParentNodeTrait {
        ParentNodeTrait::children as protected writableChildren;
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
            $this->readonlyCollection = new ReadonlyObjectCollection(
                $this->writableChildren()
            );
        }

        return $this->readonlyCollection;
    }
}
