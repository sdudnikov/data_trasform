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

    public function __construct(
        protected readonly string $name,
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

    public function child(): ConfigTreeFieldRoot
    {
        if ($this->type !== NodeInterface::TYPE_ARRAY) {
            $message = 'Field: ' . $this->name . ' cannot have child fields. It should be ' . NodeInterface::TYPE_ARRAY . ' type. ' . $this->type . ' given';
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
            $children = $childRoot->getChildren();
        }

        $this->nodesManager->addNode($this->name, $this->type, $this->outputFields, $children, $this->validator, $this->transformer);
        return $this->nodesManager->getConfigTreeRoot();
    }
}