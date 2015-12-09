<?php

namespace Nayjest\Tree\Test;

use Nayjest\Tree\Node;
use Nayjest\Tree\Tree;
use PHPUnit_Framework_TestCase;

class TreeTest extends PHPUnit_Framework_TestCase
{
    public function test()
    {
        $config = [
            'i1' => [
                'i2' => [],
                'i3' => [],
            ],
            'i4' => ['i5' => []],
        ];
        $items = [
            'i1' => $i1 = new Node(),
            'i2' => $i2 = new Node(),
            'i3' => $i3 = new Node(),
            'i4' => $i4 = new Node(),
            'i5' => $i5 = new Node(),
        ];
        $root = new Node();
        $tree = new Tree($root, $config, $items);
        $tree->build();

        // test root
        self::assertTrue($tree->getRoot() === $root);

        self::assertTrue($i1->parent() === $root, 'test tree level 1');
        self::assertTrue($i5->parent() === $i4, 'test tree level N');

        // test append
        $newNode = new Node();
        $tree->append('i4', 'new', $newNode);
        $tree->build();
        self::assertTrue($newNode->parent() === $i4);
        $items = $i4->children()->toArray();
        self::assertTrue(array_pop($items) === $newNode);

        // test remove
        $tree->remove('new');
        $tree->build();
        self::assertTrue($newNode->parent() === null);

        // test adding same node again
        $tree->prepend('i4', 'new', $newNode);
        $tree->build();
        self::assertTrue($newNode->parent() === $i4);
        self::assertTrue($i4->children()->first() === $newNode);

        // test addMany
        $tree->addMany('i5', [
            'a' => $a = new Node(),
            'b' => $b = new Node(),
        ]);
        $tree->build();
        self::assertTrue($a->parent() === $i5);
        self::assertTrue($b->parent() === $i5);

        // test move
        $tree->move('a', 'b');
        $tree->build();
        self::assertTrue($a->parent() === $b);

        $c = new Node();
        $tree->replace('a', $c);
        $tree->build();
        self::assertTrue($c->parent() === $b);
        self::assertTrue($a->parent() === null);
    }
}
