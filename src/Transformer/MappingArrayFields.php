<?php
declare(strict_types=1);

namespace DataLib\Transform\Transformer;

use DataLib\Transform\Interface\NodeInterface;
use DataLib\Transform\Interface\TransformerInterface;

class MappingArrayFields implements TransformerInterface
{
    use PipeTransform;

    /**
     * @var callable
     */
    private $mapping;

    public function __construct(callable $mapping, private readonly bool $recursive = false)
    {
        $this->mapping = $mapping;
    }

    public function transform(mixed $data, NodeInterface $node): mixed
    {
        foreach ($node->getChildren() as $child) {
            $this->map($child, $data, $this->mapping);
        }

        return $this->next($data, $node);
    }

    private function map(NodeInterface $node, array $data, callable $mapping, array $prevNodes = []): void
    {
        $field = $node->getFieldName();
        if (!isset($data[$field])) {
            return;
        }

        $mappedField = $mapping(implode('.', array_merge($prevNodes, [$field])));
        if (!$mappedField) {
            $node->transformer(new SkipField());
            return;
        }

        $node->outputFields([$mappedField]);

        if (!$this->recursive) {
            return;
        }

        foreach ($node->getChildren() as $child) {
            $this->map($child, $data[$field], $mapping, [$field]);
        }
    }
}