<?php
declare(strict_types=1);

namespace DataLib\Transform\Transformer;

use DataLib\Transform\Interface\NodeInterface;
use DataLib\Transform\Interface\TransformerInterface;

class DefaultValue implements TransformerInterface
{
    use PipeTransform;

    public function __construct(
        private $defaultValue = '',
        ?TransformerInterface $next = null
    ) {
        $this->next = $next;
    }

    public function transform(mixed $data, NodeInterface $node): mixed
    {
        if (!$data) {
            $data = $this->defaultValue;
        }

        return $this->next($data, $node);
    }
}