Tree
====

Tree data structure for PHP

[![Build Status](https://travis-ci.org/Nayjest/Tree.svg)](https://travis-ci.org/Nayjest/Tree)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Nayjest/Tree/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Nayjest/Tree/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/nayjest/tree/v/stable)](https://packagist.org/packages/nayjest/tree) 


## Requirements

* php 5.4+  

## Installation

The recommended way of installing the component is through [Composer](https://getcomposer.org).

Run following command:

```bash
composer require nayjest/tree
```

## Overview

#### Tree Nodes

Nodes are basic tree elements.
This package represents tree nodes as instances of Nayjest\Tree\Node.
Since nodes here don't holds any functionality beyond the scope of organizing into a trees, the package is designed for extending its components functionality in different ways:
- Extend base classes (Node, ReadonlyNode)
- Provide your own implementations of ParentNodeInterface and ChildNodeInterface
- Use traits (NodeTrait, ChildNodeTrait, ParentNodeTrait, ReadonlyNodeTrait) and interfaces if you can't use node classes as base classes

#### Node Collections

Each node stores its children inside instance of Nayjest\Tree\NodeCollection.

This package uses [nayjest/collections](https://github.com/Nayjest/Collection) for working with collections.

Class Nayjest\Tree\NodeCollection provides consistency of parent-child node relations. 

It means that if you will add node to collection, this node will be automatically attached to parent node associated with collection, if you will remove node from collection, node will be detached from parent node. 

#### Trees
Class Nayjest\Tree\Tree allows to:
- organize trees with **named nodes** (nodes itself don't store information about its name, class Tree works with any objects that implements NodeInterface)
- build *tree based on hierarchy configuration* (multidimantional array containing only node names) and registry of nodes(associative array where keys are names and values are nodes)
- **manipulate named nodes** in convenient way (Tree API)
- **protect tree structure** from modifying via Node API methods to avoid inconsistency of node relations.

#### Nayjest\Tree\Utils
This class is a facade for helper functions.

## Testing

Run following command:

```bash
phpunit
```

## License

© 2014—2016 Vitalii Stepanenko

Licensed under the MIT License.

Please see [License File](LICENSE) for more information.
