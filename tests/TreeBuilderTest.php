<?php

namespace Nayjest\Tree\Test;

use Nayjest\Tree\Exception\NodeNotFoundException;
use Nayjest\Tree\Node;
use Nayjest\Tree\Utils\TreeBuilder;
use PHPUnit_Framework_TestCase;

class TreeBuilderTest extends PHPUnit_Framework_TestCase
{
    public function test()
    {
        $config = [
            1 => [
                2 => [],
                3 => [],
            ],
            4 => [5],
        ];
        $items = [
            1 => $i1 = new Node(),
            2 => $i2 = new Node(),
            3 => $i3 = new Node(),
            4 => $i4 = new Node(),
            5 => $i5 = new Node(),
        ];
        $builder = new TreeBuilder();
        $tree = $builder->build($config, $items);

        self::assertEquals([$i1, $i4], array_values($tree));
        self::assertEquals(null, $i1->parent());
        self::assertEquals(null, $i4->parent());
        self::assertTrue($i1 === $i2->parent());
        self::assertTrue($i1 === $i3->parent());
        self::assertTrue($i4 === $i5->parent(), 'Test config items that requires normalization.');
    }

    public function testEmpty()
    {
        $builder = new TreeBuilder();
        self::assertEquals([], $builder->build([], []));
    }

    public function testAbsentItem()
    {
        $config = [
            1 => [2],
        ];
        $items = [
            1 => $i1 = new Node(),
            3 => $i3 = new Node(),
        ];
        $builder = new TreeBuilder();
        $exceptionThrown = false;
        try {
            $builder->build($config, $items, TreeBuilder::NORMALIZE_CONFIG);
        } catch (NodeNotFoundException $e) {
            $exceptionThrown = true;
        }
        self::assertTrue($exceptionThrown);
    }

    public function testItemOrder()
    {
        $config = ['a', 'b'];
        $itemsInReverseOrder = [
            'b' => $b = new Node(),
            'a' => $a = new Node(),
        ];
        $builder = new TreeBuilder();
        $tree = $builder->build($config, $itemsInReverseOrder);

        self::assertTrue($b === array_pop($tree));
    }
}
