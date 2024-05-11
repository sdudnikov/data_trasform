<?php
declare(strict_types=1);

namespace DataLib\Transform;

use DataLib\Transform\Interface\NodeInterface;
use DataLib\Transform\Interface\TransformerInterface;
use DataLib\Transform\Interface\ValidatorInterface;
use DataLib\Transform\Validator\Required;

class SchemaModification
{
    const MODIFICATION_TRANSFORMER = 'transformer';
    const MODIFICATION_VALIDATOR = 'validator';
    const MODIFICATION_ADDITIONAL_DATA = 'additional_data';
    const MODIFICATION_OUTPUT_FIELDS = 'output_fields';
    const MODIFICATION_FIELD_NAME = 'field_name';
    const MODIFICATION_IS_ADDED = 'is_added';
    const MODIFICATION_IS_REQUIRED = 'is_required';

    public function addModification(Schema $schema, string $key, array $modification): void
    {
        $this->validate($modification);

        $tree = $schema->getTree();
        foreach ($tree->searchByKey($key) as $node) {
            $this->setModification($node, $modification);
        }
    }

    private function validate(array $modification): void
    {
        if ($validator = $modification[self::MODIFICATION_VALIDATOR] ?? null) {
            if (!$validator instanceof ValidatorInterface) {
                throw new \Exception(self::MODIFICATION_VALIDATOR . ' value should implement ' . ValidatorInterface::class);
            }
        }

        if ($transformer = $modification[self::MODIFICATION_TRANSFORMER] ?? null) {
            if (!$transformer instanceof TransformerInterface) {
                throw new \Exception(self::MODIFICATION_TRANSFORMER . ' value should implement ' . TransformerInterface::class);
            }
        }

        if ($addData = $modification[self::MODIFICATION_ADDITIONAL_DATA] ?? null) {
            if (!is_array($addData)) {
                throw new \Exception(self::MODIFICATION_ADDITIONAL_DATA . ' value should be array');
            }
        }

        if ($outPutFields = $modification[self::MODIFICATION_OUTPUT_FIELDS] ?? null) {
            if (!is_array($outPutFields)) {
                throw new \Exception(self::MODIFICATION_OUTPUT_FIELDS . ' value should be array');
            }
        }

        if ($outPutFields = $modification[self::MODIFICATION_FIELD_NAME] ?? null) {
            if (!is_string($outPutFields)) {
                throw new \Exception(self::MODIFICATION_FIELD_NAME . ' value should be string');
            }
        }

        if ($sAdded = $modification[self::MODIFICATION_IS_ADDED] ?? null) {
            if (!is_bool($sAdded)) {
                throw new \Exception(self::MODIFICATION_IS_ADDED . ' value should be bool');
            }
        }

        if ($sRequired = $modification[self::MODIFICATION_IS_REQUIRED] ?? null) {
            if (!is_bool($sRequired)) {
                throw new \Exception(self::MODIFICATION_IS_REQUIRED . ' value should be bool');
            }
        }
    }

    private function setModification(NodeInterface $node, array $modification): void
    {
        foreach ($modification as $key => $value) {
            switch ($key) {
                case self::MODIFICATION_VALIDATOR:
                    $node->validator($value);
                    break;
                case self::MODIFICATION_TRANSFORMER:
                    $node->transformer($value);
                    break;
                case self::MODIFICATION_FIELD_NAME:
                    $node->setFieldName($value);
                    break;
                case self::MODIFICATION_ADDITIONAL_DATA:
                    $node->additionalData($value);
                    break;
                case self::MODIFICATION_OUTPUT_FIELDS:
                    $node->outputFields($value);
                    break;
                case self::MODIFICATION_IS_ADDED:
                    $node->isAdded($value);
                    break;
                case self::MODIFICATION_IS_REQUIRED:
                    if ($value) {
                        $node->isAdded(true);
                        $node->validator(new Required($node->validator()));
                    }
                    break;
            }
        }
    }
}