<?php
declare(strict_types=1);

namespace DataLib\Transform\Transformer;

use DataLib\Transform\Interface\NodeInterface;
use DataLib\Transform\Interface\TransformerInterface;

class ArrayImplode implements TransformerInterface
{
    use PipeTransform;

    public function __construct(private readonly string $separator = ',') {}

    public function transform(mixed $data, NodeInterface $node): mixed
    {
        if (!is_array($data)) {
            throw new \Exception('Field: ' . $node->getFullName() . ' should be array');
        }

        return implode($this->separator, $data);
    }
}