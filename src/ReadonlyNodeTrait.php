<?php
namespace Nayjest\Tree;

trait ReadonlyNodeTrait
{
    use ChildNodeTrait;
    use ReadonlyParentNodeTrait;
    use NodeConstructorTrait;
}
