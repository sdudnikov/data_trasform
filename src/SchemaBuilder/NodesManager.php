<?php
declare(strict_types=1);

namespace DataLib\Transform\SchemaBuilder;

use DataLib\Transform\Interface\NodeInterface;
use DataLib\Transform\Interface\TransformerInterface;
use DataLib\Transform\Interface\ValidatorInterface;
use DataLib\Transform\Node;
use DataLib\Transform\RootNode;

class NodesManager
{
    private ?RootNode $rootNode = null;

    private ConfigTreeRoot $configTreeRoot;

    public function setConfigTreeRoot(ConfigTreeRoot $configTreeRoot): void
    {
        $this->configTreeRoot = $configTreeRoot;
    }

    public function getConfigTreeRoot(): ConfigTreeRoot|ConfigTreeFieldRoot
    {
        return $this->configTreeRoot;
    }

    public function addNode(
        string $name,
        string $type,
        array $outputFields = [],
        array $children = [],
        ?ValidatorInterface $validator = null,
        ?TransformerInterface $transformer= null,
        ?bool $isAdded = false
    ): NodeInterface {
        $rootNode = $this->getRootNode();
        $node = new Node(
            $name,
            $type,
            $outputFields,
            $children,
            $validator,
            $transformer,
            $isAdded
        );
        $rootNode->addChild($node);
        return $node;
    }

    public function getRootNode(): RootNode
    {
        if (is_null($this->rootNode)) {
            $this->rootNode = RootNode::root();
        }

        return $this->rootNode;
    }
}