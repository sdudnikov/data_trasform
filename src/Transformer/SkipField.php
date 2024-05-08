<?php
declare(strict_types=1);

namespace DataLib\Transform\Transformer;

use DataLib\Transform\Interface\NodeInterface;
use DataLib\Transform\Interface\TransformerInterface;

class SkipField implements TransformerInterface
{
    use PipeTransform;

    public function __construct(private readonly bool $isSkip = true) {}

    public function transform(mixed $data, NodeInterface $node): mixed
    {
        if ($this->isSkip) {
            $node->outputFields([]);
        }

        return $this->next($data, $node);
    }
}