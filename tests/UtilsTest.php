<?php

namespace Nayjest\Tree\Test;

use Nayjest\Tree\Node;
use Nayjest\Tree\Utils;
use PHPUnit_Framework_TestCase;

class UtilsTest extends PHPUnit_Framework_TestCase
{
    /** @var  Node */
    private $root;
    /** @var  Node */
    private $attCh;
    /** @var  Node */
    private $attChL2;
    /** @var  Node */
    private $ch;
    /** @var  Node */
    private $chL2;
    /** @var  Node */
    private $chL3;

    protected function setUp()
    {
        $this->root = new Node();
        $this->attCh = new Node($this->root);
        $this->attChL2 = new Node($this->attCh);

        $this->ch = new Node();
        $this->chL2 = new Node($this->ch);
        $this->chL3 = new Node($this->chL2);
    }

    public function testApplyCallback()
    {
        $initialized = [];
        $callback = function (Node $node) use (&$initialized) {
            $initialized[] = $node;
        };
        Utils::applyCallback($callback, $this->root);
        // no duplicate calls, 1 call per node
        self::assertTrue(count($initialized) === 3);
        self::assertTrue(in_array($this->root, $initialized, true));
        self::assertTrue(in_array($this->attCh, $initialized, true));
        self::assertTrue(in_array($this->attChL2, $initialized, true));
        self::assertFalse(in_array($this->ch, $initialized, true));

        // Check init of nodes added later
        $this->attChL2->addChild($this->ch);
        self::assertTrue(count($initialized) === 6);

        // change tree structure
        $this->attChL2->detach();
        $this->attChL2->attachTo($this->root);
        // changing tree structure must not provoke duplicate init
        self::assertTrue(count($initialized) === 6);

        // assert that init works for already initialized nodes that was detached & attached again
        $this->ch->detach();
        $newNode = new Node($this->chL2);
        $this->ch->attachTo($this->attChL2);
        self::assertTrue(count($initialized) === 7);
        self::assertTrue(in_array($newNode, $initialized, true));

        $newNode->detach(); // 6 new nodes instead of 7 must be added
        $callback2 = function (Node $node) use (&$initialized) {
            $initialized[] = $node;
        };
        Utils::applyCallback($callback2, $this->root);
        self::assertTrue(count($initialized) === 13);
    }

    public function testGetDefaultTreeBuilder()
    {
        $inst = Utils::getDefaultTreeBuilder();
        self::assertTrue($inst instanceof Utils\TreeBuilder);
    }
}
