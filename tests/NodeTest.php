<?php

namespace Nayjest\Tree\Test;

use Nayjest\Tree\Node;
use PHPUnit_Framework_TestCase;

class NodeTest extends PHPUnit_Framework_TestCase
{
    /** @var  Node */
    protected $root;
    /** @var  Node */
    protected $n1;
    /** @var  Node */
    protected $n2;
    /** @var  Node */
    protected $detached;
    /** @var  Node */
    protected $n1Child;

    public function setUp()
    {
        $this->root = new Node(
            null,
            [
                $this->n1 = new Node(),
                $this->n2 = new Node(),
            ]
        );
        $this->detached = new Node();
        $this->n1Child = new Node($this->n1);
    }

    public function testInitial()
    {
        self::assertEmpty($this->n2->children());
        self::assertNull($this->root->parent());
        self::assertCount(2, $this->root->children());
        self::assertTrue($this->root->children()->contains($this->n1));
        self::assertFalse($this->root->children()->contains($this->n1Child));
        self::assertFalse($this->root->children()->contains($this->detached));
    }

    public function testAdd()
    {
        $oldRoot = new Node();
        $n = new Node($oldRoot);

        $this->root->children()->add($n);
        self::assertCount(3, $this->root->children());
        self::assertEquals($this->root, $n->parent());
        self::assertTrue($this->root->children()->contains($n));
        self::assertFalse($oldRoot->children()->contains($n));
    }

    public function testRemove()
    {
        $this->root->children()->remove($this->n1);
        self::assertCount(1, $this->root->children());
        self::assertNull($this->n1->parent());
    }

    public function testAddUsingChildNode()
    {
        self::assertNull($this->detached->parent());
        self::assertFalse($this->root->children()->contains($this->detached));

        $this->detached->attachTo($this->root);

        self::assertEquals($this->root, $this->detached->parent());
        self::assertTrue($this->root->children()->contains($this->detached));
    }

    public function testRemoveUsingChildNode()
    {
        self::assertNull($this->detached->parent());
        self::assertFalse($this->root->children()->contains($this->detached));

        self::assertEquals($this->root, $this->n1->parent());
        self::assertTrue($this->root->children()->contains($this->n1));

        $this->n1->detach();

        self::assertNull($this->n1->parent());
        self::assertFalse($this->root->children()->contains($this->n1));
    }
}
