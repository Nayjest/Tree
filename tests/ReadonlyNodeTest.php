<?php

namespace Nayjest\Tree\Test;

use Nayjest\Tree\Exception\ReadonlyNodeModifyException;
use Nayjest\Tree\Node;
use Nayjest\Tree\ReadonlyNode;
use PHPUnit_Framework_TestCase;

class ReadonlyNodeTest extends PHPUnit_Framework_TestCase
{
    public function test()
    {
        $child1 = new Node();
        $child2 = new ReadonlyNode();
        $detached1 = new Node();
        $detached2 = new ReadonlyNode();
        $node = new ReadonlyNode(null, [
            $child1,
            $child2,
        ]);
        self::assertTrue($node->children()->contains($child1));
        self::assertTrue($node->children()->contains($child2));
        self::assertFalse($node->children()->contains($detached1));
        self::assertFalse($node->children()->contains($detached2));

        self::assertFalse($node->children()->isWritable());
        self::assertFalse(method_exists($node->children(), 'addItem'));

        $expectedE = null;
        try {
            $child1->detach();
        } catch (ReadonlyNodeModifyException $e) {
            $expectedE = $e;
        }
        self::assertNotEmpty($expectedE);
        self::assertTrue($node->children()->contains($child1));

        $expectedE = null;
        try {
            $child2->detach();
        } catch (ReadonlyNodeModifyException $e) {
            $expectedE = $e;
        }
        self::assertNotEmpty($expectedE);
        self::assertTrue($node->children()->contains($child2));

        $expectedE = null;
        try {
            $detached1->attachTo($node);
        } catch (ReadonlyNodeModifyException $e) {
            $expectedE = $e;
        }
        self::assertNotEmpty($expectedE);
        self::assertFalse($node->children()->contains($detached1));

        $expectedE = null;
        try {
            $detached2->attachTo($node);
        } catch (ReadonlyNodeModifyException $e) {
            $expectedE = $e;
        }
        self::assertNotEmpty($expectedE);
        self::assertFalse($node->children()->contains($detached2));
    }
}
