<?php
declare(strict_types=1);

namespace DataLib\Transform;

final class RootNode extends Node
{
    private ?array $flat = null;

    static public function root(): self
    {
        return new self('root', 'root');
    }

    /**
     * @param string $pattern
     * @return Node []
     */
    public function search(string $pattern): iterable
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

    private function getFlat(): array
    {
        if (is_null($this->flat)) {
            $this->fillFlat($this);
        }
        return $this->flat;
    }

    private function fillFlat(Node $node): void
    {
        if ($node->getFullName() != 'root') {
            $this->flat[$node->getFullName()] = $node;
        }

        foreach ($node->getChildren() as $child) {
            $this->fillFlat($child);
        }
    }
}