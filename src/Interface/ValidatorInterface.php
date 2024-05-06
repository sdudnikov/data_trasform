<?php
declare(strict_types=1);

namespace DataLib\Transform\Interface;

interface ValidatorInterface
{
    public function validate(mixed $data, NodeInterface $node);
}