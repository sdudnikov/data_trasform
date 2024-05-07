<?php
declare(strict_types=1);

namespace DataLib\Transform\Validator;

use DataLib\Transform\Interface\NodeInterface;
use DataLib\Transform\Interface\ValidatorInterface;

class NotEmpty implements ValidatorInterface
{
    public function validate(mixed $data, NodeInterface $node): void
    {
       if (empty($data)) {
           throw new \Exception('Field: ' . $node->getFullName() . ' cannot be empty');
       }
    }
}