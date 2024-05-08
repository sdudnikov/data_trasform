<?php
declare(strict_types=1);

namespace DataLib\Transform\SchemaBuilder;

use DataLib\Transform\Interface\NodeInterface;
use DataLib\Transform\Interface\TransformerInterface;
use DataLib\Transform\Interface\ValidatorInterface;
use DataLib\Transform\RootNode;
use DataLib\Transform\Validator\Required;

class ConfigTreeField
{
    protected ?TransformerInterface $transformer = null;
    protected bool $isTransformerInherited = false;
    protected ?ValidatorInterface $validator = null;
    protected bool $isValidatorInherited = false;
    protected array $outputFields = [];
    protected ?NodesManager $childNodesManager = null;
    protected ?bool $isAdded = null;
    protected bool $isAddedInherited = false;
    protected array $additionalData = [];
    protected bool $isAddDataInherited = false;
    protected bool $required = false;

    public function __construct(
        protected readonly array $names,
        protected readonly string $type,
        protected readonly NodesManager $nodesManager
    ) {}

    public function validator($validator, bool $isInherited = false): self
    {
        $this->isValidatorInherited = $isInherited;
        $this->validator = $validator;
        return $this;
    }

    public function transformer($transformer, bool $isInherited = false): self
    {
        $this->isTransformerInherited = $isInherited;
        $this->transformer = $transformer;
        return $this;
    }

    public function outputFields(array $outputFields): self
    {
        if (count($this->names) > 1) {
            throw new \Exception('You can set outputFields only form one field name');
        }

        $this->outputFields = $outputFields;
        return $this;
    }

    public function required(bool $required = true): self
    {
        $this->required = $required;
        return $this;
    }

    public function isAdded(bool $isAdded, bool $isInherited = false): self
    {
        $this->isAdded = $isAdded;
        $this->isAddedInherited = $isInherited;
        return $this;
    }

    public function additionalData(array $additionalData, bool $isInherited = false): self
    {
        $this->isAddDataInherited = $isInherited;
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
                !$this->outputFields ? [$name] : $this->outputFields,
                $children,
                $this->validator,
                $this->transformer,
                $this->isAdded
            );

            $node->additionalData($this->additionalData);
            $this->setRequired($node);
        }

        return $this->nodesManager->getConfigTreeRoot();
    }

    private function inheritProperties(NodeInterface $node): void
    {
        if (!$node instanceof RootNode) {
            $this->inherit($node);
        }

        foreach ($node->getChildren() as $child) {
            $this->inheritProperties($child);
        }
    }

    private function inherit(NodeInterface $node): void
    {
        if ($this->isAddedInherited) {
            if (is_null($node->isAdded())) {
                $node->isAdded($this->isAdded);
            }
        }

        if ($this->isTransformerInherited) {
            if (is_null($node->transformer())) {
                $node->transformer($this->transformer);
            }
        }

        if ($this->isValidatorInherited) {
            if (is_null($node->validator())) {
                $node->validator($this->validator);
            }
        }

        if ($this->isAddDataInherited) {
            if (is_null($node->additionalData())) {
                $node->additionalData($this->additionalData);
            }
        }
    }

    private function setRequired(NodeInterface $node): void
    {
        if ($this->required) {
            $node->isAdded(true);
            $node->validator(new Required($node->validator()));
        }
    }
}