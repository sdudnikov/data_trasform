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

    public function setFieldName(string $fieldName): void;

    public function getFieldName(): string;

    public function getFieldType(): string;

    public function getOutputFields(): array;

    public function getValidator(): ?ValidatorInterface;

    public function getTransformer(): ?TransformerInterface;

    /**
     * @return NodeInterface []
     */
    public function getChildren(): array;

    public function addChild(NodeInterface $node): void;

    public function getFullName(): string;

    public function getPrentNode(): ?NodeInterface;

    public function setParentNode(NodeInterface $parent): void;

    public function setAdditionalData(array $additionalData): void;

    public function getAdditionalData(): array;

    public function isSet(?bool $flag = null): bool;
}