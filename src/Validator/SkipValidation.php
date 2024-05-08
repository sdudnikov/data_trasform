<?php
declare(strict_types=1);

namespace DataLib\Transform\Validator;

use DataLib\Transform\Interface\NodeInterface;
use DataLib\Transform\Interface\ValidatorInterface;

class SkipValidation implements ValidatorInterface
{
    public function validate(mixed $data, NodeInterface $node): void
    {
        return;
    }
}