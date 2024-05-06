<?php
declare(strict_types=1);

namespace DataLib\Transform\Validator;

use DataLib\Transform\Interface\NodeInterface;
use DataLib\Transform\Interface\ValidatorInterface;

class CompositeValidator implements ValidatorInterface
{
    public function __construct(
        protected array $validators = []
    ) {}

    public function validate(mixed $data, NodeInterface $node)
    {
        /** @var ValidatorInterface $validator */
        foreach ($this->validators as $validator) {
            if (!$validator instanceof ValidatorInterface) {
                $class = get_class($validator);
                throw new \Exception('Class: '. $class . ' should implement ' . ValidatorInterface::class);
            }

            $validator->validate($data, $node);
        }
    }

    public function addValidator(ValidatorInterface $validator): void
    {
        $this->validators[] = $validator;
    }
}