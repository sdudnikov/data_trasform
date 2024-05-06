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

    protected bool $isSet = true;

    public function __construct(
        protected string $fieldName,
        protected string $fieldType,
        protected array $outputFields = [],
        protected array $children = [],
        protected ?ValidatorInterface $validator = null,
        protected ?TransformerInterface $transformer = null
    ) {}

    public function getChildren(): array
    {
        return $this->children;
    }

    public function getTransformer(): ?TransformerInterface
    {
        return $this->transformer;
    }


    public function setFieldName(string $fieldName): void
    {
        $this->fieldName = $fieldName;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getFieldType(): string
    {
        return $this->fieldType;
    }

    public function getOutputFields(): array
    {
        if (!$this->outputFields) {
            return [$this->fieldName];
        }

        return $this->outputFields;
    }

    public function getValidator(): ?ValidatorInterface
    {
        return $this->validator;
    }

    public function addChild(NodeInterface $node): void
    {
        $this->children[$node->getFieldName()] = $node;
        $node->setParentNode($this);
    }

    public function setParentNode(NodeInterface $parent): void
    {
        if (!$parent instanceof RootNode) {
            $this->parentNode = $parent;
        }
    }

    public function getPrentNode(): ?NodeInterface
    {
        return $this->parentNode;
    }

    public function getFullName(): string
    {
        if (!$this->getPrentNode()) {
            return $this->getFieldName();
        }

        return $this->getPrentNode()->getFullName() . '.' . $this->getFieldName();
    }

    public function setAdditionalData(array $additionalData): void
    {
        $this->additionalData = $additionalData;
    }

    public function getAdditionalData(): array
    {
        return $this->additionalData;
    }

    public function isSet(?bool $flag = null): bool
    {
        if (is_null($flag)) {
            return $this->isSet;
        }

        $this->isSet = $flag;
        return $this->isSet;
    }
}