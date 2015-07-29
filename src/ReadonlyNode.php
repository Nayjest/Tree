<?php

namespace Nayjest\Tree;

class ReadonlyNode implements NodeInterface
{
    use NodeConstructorTrait;
    use ReadonlyNodeTrait;
}
