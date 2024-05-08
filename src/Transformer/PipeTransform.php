<?php
declare(strict_types=1);

namespace DataLib\Transform\Transformer;

use DataLib\Transform\Interface\NodeInterface;
use DataLib\Transform\Interface\TransformerInterface;

trait PipeTransform
{
    protected ?TransformerInterface $next = null;

    public function __construct(?TransformerInterface $next = null)
    {
        $this->next = $next;
    }

    public function next(mixed $data, NodeInterface $node): mixed
    {
        if (is_null($this->next)) {
            return $data;
        }

        return $this->next->transform($data, $node);
    }

    public function setNext(?TransformerInterface $next): void
    {
        $this->next = $next;
    }
}