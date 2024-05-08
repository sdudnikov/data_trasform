<?php
declare(strict_types=1);

namespace DataLib\Transform\Transformer;

use DataLib\Transform\Interface\NodeInterface;
use DataLib\Transform\Interface\TransformerInterface;

class ArrayMerge implements TransformerInterface
{
    use PipeTransform;

    public function __construct(private readonly array $array) {}

    public function transform(mixed $data, NodeInterface $node): mixed
    {
        if (is_array($data)) {
            $data = array_merge($this->array, $data);
        }

        return $this->next($data, $node);
    }
}