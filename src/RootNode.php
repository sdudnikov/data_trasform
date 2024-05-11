<?php
declare(strict_types=1);

namespace DataLib\Transform;

use DataLib\Transform\Interface\NodeInterface;
use DataLib\Transform\Interface\TransformerInterface;
use DataLib\Transform\Interface\ValidatorInterface;

final class RootNode implements NodeInterface
{
    private array $children = [];
    private ?array $flat = null;

    static public function root(): self
    {
        return new self();
    }

    public function addChild(NodeInterface $node): void
    {
        $this->flat = null;
        $this->children[$node->getFieldName()] = $node;
        $node->setParentNode($this);
    }

    /**
     * @return NodeInterface []
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param string $pattern
     * @return NodeInterface []
     */
    public function searchByKey(string $pattern): iterable
    {
        $flat = $this->getFlat();
        foreach ($flat as $key => $node) {
            $patternParts = explode('.', $pattern);
            $keyParts = explode('.', $key);
            if (count($patternParts) != count($keyParts)) {
                continue;
            }

            $foundKeys = [];
            foreach ($patternParts as $indx => $patternPart) {
                if ($patternPart == '*') {
                    $foundKeys[] = $keyParts[$indx];
                    continue;
                }

                if ($patternPart == $keyParts[$indx]) {
                    $foundKeys[] = $keyParts[$indx];
                }
            }

            if (count($foundKeys) != count($keyParts)) continue;
            yield $flat[implode('.', $foundKeys)];
        }

        return null;
    }

    /**
     * @param string [] $types
     * @return NodeInterface []
     */
    public function searchByType(array $types): iterable
    {
        foreach ($this->recursiveSearchByType($types, $this) as $node) {
            yield $node;
        }
    }

    /**
     * @param string [] $types
     * @param NodeInterface $node
     * @return iterable
     */
    protected function recursiveSearchByType(array $types, NodeInterface $node): iterable
    {
        if (!$node instanceof RootNode && in_array($node->getFieldType(), $types)) {
            yield $node;
        }

        foreach ($node->getChildren() as $child) {
            foreach ($this->recursiveSearchByType($types, $child) as $node) {
                yield $node;
            }
        }

        return null;
    }

    public function walk(callable $callback): void
    {
        foreach ($this->getChildren() as $child) {
            $this->walkNode($child, $callback);
        }
    }

    public function resetFlat(): void
    {
        $this->flat = null;
    }

    protected function walkNode(NodeInterface $node, callable $callback): void
    {
        $callback($node);
        foreach ($node->getChildren() as $child) {
            $this->walkNode($child, $callback);
        }
    }

    protected function getFlat(): array
    {
        if (is_null($this->flat)) {
            $this->fillFlat($this);
        }
        return $this->flat;
    }

    private function fillFlat($node): void
    {
        if (!$node instanceof RootNode) {
            $this->flat[$node->getFullName()] = $node;
        }

        foreach ($node->getChildren() as $child) {
            $this->fillFlat($child);
        }
    }

    public function setFieldName(string $fieldName): void
    {
        throw new \Exception('Not allowed set field name to root node');
    }

    public function getFieldName(): string
    {
        return '';
    }

    public function getFieldType(): string
    {
        throw new \Exception('Not allowed get type field name from root node');
    }

    public function outputFields(?array $outputFields = null): ?array
    {
        throw new \Exception('Not allowed get or set outputFields for root node');
    }

    public function validator(?ValidatorInterface $validator = null): ?ValidatorInterface
    {
        throw new \Exception('Not allowed get or set validator for root node');
    }

    public function transformer(?TransformerInterface $transformer = null): ?TransformerInterface
    {
        throw new \Exception('Not allowed get or set transformer for root node');
    }

    public function getFullName(): string
    {
        return '';
    }

    public function getParentNode(): ?NodeInterface
    {
        return null;
    }

    public function setParentNode(NodeInterface $parent): void
    {
        throw new \Exception('Not allowed set parent node to root node');
    }

    public function additionalData(?array $additionalData = null): ?array
    {
        throw new \Exception('Not allowed get or set additionalData for root node');
    }

    public function isAdded(?bool $isAdded = null): ?bool
    {
        throw new \Exception('Not allowed get or set isAdded for root node');
    }

    public function getNotSetValue(): mixed
    {
        throw new \Exception('Not allowed getNotSetValue for root node');
    }

    public function isNotSet(?bool $isNotSet = null): bool
    {
        throw new \Exception('Not allowed get or set isNotSet for root node');
    }
}