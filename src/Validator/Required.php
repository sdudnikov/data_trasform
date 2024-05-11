<?php
declare(strict_types=1);

namespace DataLib\Transform\Validator;

use DataLib\Transform\Interface\NodeInterface;
use DataLib\Transform\Interface\ValidatorInterface;

class Required implements ValidatorInterface
{
    public function __construct(private readonly ?ValidatorInterface $validator = null) {}

    public function validate(mixed $data, NodeInterface $node): void
    {
        if ($node->isNotSet()) {
            throw new \Exception('Field: ' . $node->getFullName() . ' is required');
        }

        $this->validator?->validate($data, $node);
    }
}