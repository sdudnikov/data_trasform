<?php
declare(strict_types=1);

namespace DataLib\Transform\Validator;

use DataLib\Transform\Interface\NodeInterface;
use DataLib\Transform\Interface\ValidatorInterface;

class StringLength implements ValidatorInterface
{
    public function __construct(
        private readonly int $min = 100,
        private readonly int $max = 1000
    ) {}

    public function validate(mixed $data, NodeInterface $node): void
    {
        if (!is_string($data)) {
            return;
        }

        $length = strlen($data);
        if ($length < $this->min) {
            throw new \Exception('Field: ' . $node->getFullName() . ' has length less than ' . $this->min);
        }

        if ($length > $this->max) {
            throw new \Exception('Field: ' . $node->getFullName() . ' has length more than ' . $this->max);
        }
    }
}