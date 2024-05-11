<?php
declare(strict_types=1);

namespace DataLib\Transform\Interface;

interface NodeInterface
{
    const TYPE_SCALAR = 'scalar';
    const TYPE_STRING = 'string';
    const TYPE_ARRAY = 'array';
    const TYPE_INT = 'integer';
    const TYPE_FLOAT = 'float';
    const TYPE_BOOL = 'boolean';
    const TYPE_NULL = 'NULL';

    public function setFieldName(string $fieldName): void;

    public function getFieldName(): string;

    public function getFieldType(): string;

    public function outputFields(?array $outputFields = null): ?array;

    public function validator(?ValidatorInterface $validator = null): ?ValidatorInterface;

    public function transformer(?TransformerInterface $transformer = null): ?TransformerInterface;

    /**
     * @return NodeInterface []
     */
    public function getChildren(): array;

    public function addChild(NodeInterface $node): void;

    public function getFullName(): string;

    public function getParentNode(): ?NodeInterface;

    public function setParentNode(NodeInterface $parent): void;

    public function additionalData(?array $additionalData = null): ?array;

    public function isAdded(?bool $isAdded = null): ?bool;

    public function isNotSet(?bool $isNotSet = null): bool;

    public function getNotSetValue(): mixed;
}