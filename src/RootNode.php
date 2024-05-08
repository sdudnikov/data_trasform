<?php
declare(strict_types=1);

namespace DataLib\Transform;

use DataLib\Transform\Interface\NodeInterface;

final class RootNode
{
    protected array $children = [];
    private ?array $flat = null;

    static public function root(): self
    {
        return new self();
    }

    public function addChild(NodeInterface $node): void
    {
        $this->children[$node->getFieldName()] = $node;
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
     * @return Node []
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

            $foundKey = '';
            foreach ($patternParts as $indx => $patternPart) {
                if ($patternPart == '*') {
                    $foundKey .= $keyParts[$indx];
                    continue;
                }

                if ($patternPart == $keyParts[$indx]) {
                    $foundKey .= $keyParts[$indx];
                }
            }

            if (!$foundKey) continue;
            yield $flat[$foundKey];
        }

        return null;
    }

    /**
     * @param string $type
     * @return Node []
     */
    public function searchByType(string $type): iterable
    {
        foreach ($this->recursiveSearchByType($type, $this) as $node) {
            yield $node;
        }
    }

    protected function recursiveSearchByType(string $type, $node): iterable
    {
        if (!$node instanceof RootNode && $node->getFieldType() == $type) {
            yield $node;
        }

        foreach ($node->getChildren() as $child) {
            foreach ($this->recursiveSearchByType($type, $child) as $node) {
                yield $node;
            }
        }

        return null;
    }

    private function getFlat(): array
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
}