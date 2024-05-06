<?php
declare(strict_types=1);

namespace DataLib\Transform\SchemaBuilder;

use DataLib\Transform\Interface\NodeInterface;
use DataLib\Transform\Interface\TransformerInterface;
use DataLib\Transform\Interface\ValidatorInterface;

class ConfigTreeField
{
    protected ?TransformerInterface $transformer = null;
    protected ?ValidatorInterface $validator = null;
    protected array $outputFields = [];
    protected ?NodesManager $childNodesManager = null;
    protected ?bool $isAdded = null;
    protected bool $isAddedInherited = false;
    protected array $additionalData = [];

    public function __construct(
        protected readonly array $names,
        protected readonly string $type,
        protected readonly NodesManager $nodesManager
    ) {}

    public function validator($validator): self
    {
        $this->validator = $validator;
        return $this;
    }

    public function transformer($transformer): self
    {
        $this->transformer = $transformer;
        return $this;
    }

    public function outputFields(array $outputFields): self
    {
        $this->outputFields = $outputFields;
        return $this;
    }

    public function isAdded(bool $isAdded, bool $isInherited = false): self
    {
        $this->isAdded = $isAdded;
        $this->isAddedInherited = $isInherited;
        return $this;
    }

    public function additionalData(array $additionalData): self
    {
        $this->additionalData = $additionalData;
        return $this;
    }

    public function child(): ConfigTreeFieldRoot
    {
        if ($this->type !== NodeInterface::TYPE_ARRAY) {
            $message = 'Field(s): ' . implode(',', $this->names) . ' cannot have child fields. It should be ' .
                NodeInterface::TYPE_ARRAY . ' type. ' . $this->type . ' given';
            throw new \Exception($message);
        }

        $this->childNodesManager = new NodesManager();
        return new ConfigTreeFieldRoot($this->childNodesManager, $this);
    }

    public function end(): ConfigTreeRoot|ConfigTreeFieldRoot
    {
        $children = [];
        if (!is_null($this->childNodesManager)) {
            $childRoot = $this->childNodesManager->getRootNode();
            $this->inheritProperties($childRoot);
            $children = $childRoot->getChildren();
        }

        foreach ($this->names as $name) {
            $node = $this->nodesManager->addNode($name,
                $this->type,
                $this->outputFields,
                $children,
                $this->validator,
                $this->transformer,
                $this->isAdded
            );

            $node->setAdditionalData($this->additionalData);
        }

        return $this->nodesManager->getConfigTreeRoot();
    }

    private function inheritProperties(NodeInterface $node): void
    {
        if ($this->isAddedInherited) {
            foreach ($node->getChildren() as $child) {
                if (is_null($child->isAdded())) {
                    $child->isAdded($this->isAdded);
                }
            }
        }
    }
}