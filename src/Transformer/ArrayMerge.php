<?php
declare(strict_types=1);

namespace DataLib\Transform\Transformer;

use DataLib\Transform\Interface\NodeInterface;
use DataLib\Transform\Interface\TransformerInterface;

class ArrayMerge implements TransformerInterface
{
    use PipeTransform;

    public function __construct(private readonly array $defaultValues)
    {}

    public function transform(mixed $data, NodeInterface $node): mixed
    {
        return array_merge($this->defaultValues, $data);
    }
}