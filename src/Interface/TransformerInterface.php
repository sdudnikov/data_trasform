<?php
declare(strict_types=1);

namespace DataLib\Transform\Interface;

interface TransformerInterface
{
    public function transform(mixed $data, NodeInterface $node): mixed;
}