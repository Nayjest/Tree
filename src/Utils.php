<?php

namespace Nayjest\Tree;

use Nayjest\Tree\Utils\TreeBuilder;

class Utils
{
    private static $treeBuilder;

    /**
     * @return TreeBuilder
     */
    public static function getDefaultTreeBuilder()
    {
        if (!self::$treeBuilder) {
            self::$treeBuilder = new TreeBuilder();
        }

        return self::$treeBuilder;
    }

    /**
     * Builds tree from plain nodes based on configuration.
     *
     * @param array           $config     multidimensional array that represents tree structure
     * @param NodeInterface[] $plainItems nodes that must be organized to tree
     * @param int             $flags      specifies tree building options, default: TreeBuilder::NORMALIZE_CONFIG; see TreeBuilder constants
     *
     * @return NodeInterface[] items organized to tree structure; array keys are not preserved
     */
    public static function buildTree(array $config, array $plainItems, $flags = TreeBuilder::NORMALIZE_CONFIG)
    {
        return self::getDefaultTreeBuilder()->build($config, $plainItems, $flags);
    }

    /**
     * Applies callback to root node, if it's existing and further descendant nodes directly after adding to tree.
     *
     * @param callable      $callback    function to apply
     * @param NodeInterface $root        root node
     * @param string        $targetClass callback will be applied only to nodes that are instances of $targetClass or inherited classes
     */
    public static function applyCallback(callable $callback, NodeInterface $root, $targetClass = ChildNodeInterface::class)
    {
        $processed = [];
        $f = function (ChildNodeInterface $targetNode) use ($callback, $targetClass, &$f, &$processed) {
            if (in_array($targetNode, $processed, true)) {
                return;
            }
            $nodes = $targetNode instanceof ParentNodeInterface
                ? $targetNode->getChildrenRecursive()->toArray()
                : [];
            $nodes[] = $targetNode;

            /** @var NodeInterface $node */
            foreach ($nodes as $node) {
                $node instanceof $targetClass && call_user_func($callback, $node);
                $node instanceof ParentNodeInterface && $node->children()->onItemAdd($f);
                $processed[] = $node;
            }
        };
        $f($root);
    }
}
