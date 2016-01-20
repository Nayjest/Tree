<?php

namespace Nayjest\Tree;

use Nayjest\Collection\Decorator\ReadonlyObjectCollection;

trait ReadonlyNodeTrait
{
    use ChildNodeTrait;
    use ParentNodeTrait {
        ParentNodeTrait::children as protected writableChildren;
    }

    private $readonlyCollection;

    /**
     * Returns child components.
     *
     * @return ReadonlyObjectCollection
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
