<?php
declare(strict_types=1);

namespace DataLib\Transform\Transformer;

use DataLib\Transform\Interface\NodeInterface;
use DataLib\Transform\Interface\TransformerInterface;

class CompleteSchema implements TransformerInterface
{
    use PipeTransform;

    public function transform(mixed $data, NodeInterface $node): mixed
    {
        if ($node->isSet()) {
            return $this->next($data, $node);
        }

        $node->isSet(true);
        $transformer = $node->getTransformer();
        if ($transformer) {
            $data = $transformer->transform($data, $node);
        }

        return $data;
    }
}