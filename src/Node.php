<?php
declare(strict_types=1);

namespace DataLib\Transform;

use DataLib\Transform\Interface\NodeInterface;
use DataLib\Transform\Interface\ValidatorInterface;
use DataLib\Transform\Interface\TransformerInterface;

class Node implements NodeInterface
{
    protected ?NodeInterface $parentNode = null;
    protected array $additionalData = [];
    protected array $children = [];
    private ?RootNode $rootNode = null;

    public function __construct(
        protected string $fieldName,
        protected string $fieldType,
        protected array $outputFields = [],
        array $children = [],
        protected ?ValidatorInterface $validator = null,
        protected ?TransformerInterface $transformer = null,
        protected ?bool $isAdded = null
    ) {
        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function setFieldName(string $fieldName): void
    {
        $this->fieldName = $fieldName;
        $this->resetFlatInRoot();
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getFieldType(): string
    {
        return $this->fieldType;
    }

    public function addChild(NodeInterface $node): void
    {
        $this->children[$node->getFieldName()] = $node;
        $node->setParentNode($this);
        $this->resetFlatInRoot();
    }

    public function setParentNode(NodeInterface $parent): void
    {
        $this->parentNode = $parent;
    }

    public function getParentNode(): ?NodeInterface
    {
        return $this->parentNode;
    }

    public function getFullName(): string
    {
        if ($this->getParentNode() instanceof RootNode) {
            return $this->getFieldName();
        }

        return $this->getParentNode()->getFullName() . '.' . $this->getFieldName();
    }

    public function getNotSetValue(): mixed
    {
        return match ($this->getFieldType()) {
            NodeInterface::TYPE_STRING => '',
            NodeInterface::TYPE_NULL => null,
            NodeInterface::TYPE_ARRAY => [],
            NodeInterface::TYPE_INT => 0,
            NodeInterface::TYPE_FLOAT => 0.0,
            NodeInterface::TYPE_BOOL, NodeInterface::TYPE_SCALAR => null
        };
    }

    public function isAdded(?bool $isAdded = null): ?bool
    {
        if (!is_null($isAdded)) {
            $this->isAdded = $isAdded;
        }

        return $this->isAdded;
    }

    public function validator(?ValidatorInterface $validator = null): ?ValidatorInterface
    {
        if (!is_null($validator)) {
            $this->validator = $validator;
        }

        return $this->validator;
    }

    public function transformer(?TransformerInterface $transformer = null): ?TransformerInterface
    {
        if (!is_null($transformer)) {
            $this->transformer = $transformer;
        }

        return $this->transformer;
    }

    public function additionalData(?array $additionalData = null): ?array
    {
        if (!is_null($additionalData)) {
            $this->additionalData = $additionalData;
        }

        return $this->additionalData;
    }

    public function outputFields(?array $outputFields = null): ?array
    {
        if (!is_null($outputFields)) {
            $this->outputFields = $outputFields;
        }

        return $this->outputFields;
    }

    protected function resetFlatInRoot(): void
    {
        if ($root = $this->getRootNode()) {
            $root->resetFlat();
        }
    }

    protected function getRootNode(): ?RootNode
    {
        if (!is_null($this->rootNode)) {
            return $this->rootNode;
        }

        $root = $this->getParentNode();
        if (is_null($root)) {
            return null;
        }

        while (!$root instanceof RootNode) {
            $root = $root->getParentNode();
        }
        $this->rootNode = $root;

        return $this->rootNode;
    }
}