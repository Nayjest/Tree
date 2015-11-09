<?php

namespace Nayjest\Tree;

use Nayjest\Tree\Exception\InvalidTreeConfigException;

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
    public function build(array $config, array $plainItems, $flags = TreeBuilder::NORMALIZE_CONFIG)
    {
        // preprocess config if needed
        if ($flags & TreeBuilder::NORMALIZE_CONFIG) {
            $config = $this->normalizeConfig($config);
        }
        $currentLevelItems = [];
        foreach ($config as $key => $itemConfig) {
            // check that item specified in $config exists.
            if (!array_key_exists($key, $plainItems)) {
                if ($flags & TreeBuilder::ALLOW_ABSENT_ITEMS) {
                    continue;
                }
                throw new InvalidTreeConfigException(
                    'Error building tree: '
                    . "Can't find item by '$key' key that's used in tree configuration."
                );
            }

            /** @var NodeInterface $item */
            $currentLevelItems[] = $item = $plainItems[$key];

            // attach children
            $itemChildren = $this->build(
                $itemConfig,
                $plainItems,
                // config must be already normalized, so remove self::NORMALIZE_CONFIG flag on recursive call
                $flags ^ TreeBuilder::NORMALIZE_CONFIG
            );
            if (count($itemChildren) === 0) {
                continue;
            }
            if(!$item->isWritable()) {
                throw new InvalidTreeConfigException(
                    'Error building tree: '
                    . "Can't attach children to '$key' node that is'nt writable."
                );
            }
            $item->addChildren($itemChildren);
        }
        return $currentLevelItems;
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
