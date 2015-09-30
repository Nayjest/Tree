<?php

namespace Nayjest\Tree;

use Nayjest\Tree\Exceptions\InvalidTreeConfigException;

/**
 * TreeBuilder class allows to organize plain nodes into a tree based on configuration.
 */
class TreeBuilder
{
    /**
     * Transforms array in form [1,2 => [3]] to [1=>[], 2=>[3=>[]]]
     */
    const NORMALIZE_CONFIG = 1;

    /**
     * Allows absent nodes in config
     */
    const ALLOW_ABSENT_ITEMS = 2;

    /**
     * Builds tree from plain nodes based on configuration.
     *
     * @param array $config multidimensional array that represents tree structure
     * @param NodeInterface[] $plainItems nodes that must be organized to tree
     * @param int $flags specifies tree building options, default: TreeBuilder::NORMALIZE_CONFIG; see TreeBuilder constants
     * @return NodeInterface[] items organized to tree structure; array keys are not preserved
     */
    public function build(array $config, array $plainItems, $flags = self::NORMALIZE_CONFIG)
    {
        // preprocess config if needed
        if ($flags & self::NORMALIZE_CONFIG) {
            $config = $this->normalizeConfig($config);
        }
        // resolve nodes on current tree level
        $treeLevel = array_intersect_key($plainItems, $config);

        foreach ($config as $key => $itemConfig) {

            // check that item specified in $config exists.
            if (!array_key_exists($key, $treeLevel)) {
                if ($flags & self::ALLOW_ABSENT_ITEMS) {
                    continue;
                } else {
                    throw new InvalidTreeConfigException(
                        'Error building tree: '
                        . "Can't find item by '$key' key that's used in tree configuration."
                    );
                }
            }

            // attach parents to children
            /** @var NodeInterface $item */
            $item = $treeLevel[$key];
            $itemChildren = $this->build($itemConfig, $plainItems, $flags);
            $item->addChildren($itemChildren);
        }
        return $treeLevel;
    }

    /**
     * Transforms array in form [1,2 => [3]] to [1=>[], 2=>[3=>[]]].
     *
     * @param array $config
     * @return array
     */
    protected function normalizeConfig(array $config)
    {
        $final = [];
        foreach($config as $key => $value) {
            if (is_array($value)) {
                $final[$key] = $this->normalizeConfig($value);
            } else {
                $final[$value] = [];
            }
        }
        return $final;
    }
}
