<?php
declare(strict_types=1);

namespace DataLib\Transform\Validator;

use DataLib\Transform\Interface\NodeInterface;
use DataLib\Transform\Interface\ValidatorInterface;

class Required implements ValidatorInterface
{
    public function validate(mixed $data, NodeInterface $node)
    {
        if (!$data) {
            throw new \Exception('Field: ' . $node->getFullName() . ' is required');
        }
    }
}