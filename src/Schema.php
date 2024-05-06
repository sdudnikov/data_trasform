<?php
declare(strict_types=1);

namespace DataLib\Transform;

use DataLib\Transform\Interface\NodeInterface;
use DataLib\Transform\Interface\SchemaInterface;

class Schema implements SchemaInterface
{
    const ALL_KEYS = '*';

    private array $state = [];

    public function __construct(
        protected string $name,
        protected RootNode $tree
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getTree(): RootNode
    {
        return $this->tree;
    }

    public function transform(array $data): array
    {
        $tree = $this->getTree();
        return $this->transformChildren($tree, $data);
    }

    protected function recursiveTransform($data, NodeInterface $node)
    {
        $validator = $node->getValidator();
        $transformer = $node->getTransformer();

        $this->typeValidation($data, $node);
        $validator?->validate($data, $node);
        if ($transformer) {
            $data = $transformer->transform($data, $node);
        }

        $children = $node->getChildren();
        if (!is_array($data) || !$children) {
            return $data;
        }

        return $this->transformChildren($node, $data);
    }

    private function transformChildren($root, $data)
    {
        $result = [];
        foreach ($root->getChildren() as $node) {
            $fields = [$node->getFieldName()];
            if ($node->getFieldName() === self::ALL_KEYS) {
                $fields = array_keys($data);
            }

            foreach ($fields as $field) {
                $this->storeNodeState($node);
                $node->setFieldName((string) $field);

                if (!array_key_exists($node->getFieldName(), $data)) {
                    $dataToTransform = $this->getNotSetValue($node);
                    $node->isSet(false);
                } else {
                    $dataToTransform = $data[$node->getFieldName()];
                }

                $transformed = $this->recursiveTransform($dataToTransform, $node);
                $this->setOutputFields($node, $transformed, $result);
                $this->restoreNodeState($node);
            }
        }

        return $result;
    }

    private function typeValidation($data, NodeInterface $node)
    {
        if (is_null($data) || !$node->isSet()) {
            return;
        }

        $type = $node->getFieldType();
        if ($type == NodeInterface::TYPE_SCALAR) {
            if (!is_scalar($data)) {
                throw new \Exception('Field: ' . $node->getFullName() . ' should be ' . NodeInterface::TYPE_SCALAR);
            }
        }

        if ($type == NodeInterface::TYPE_STRING) {
            if (!is_string($data)) {
                throw new \Exception('Field: ' . $node->getFullName() . ' should be ' . NodeInterface::TYPE_STRING);
            }
        }

        if ($type == NodeInterface::TYPE_ARRAY) {
            if (!is_array($data)) {
                throw new \Exception('Field: ' . $node->getFullName() . ' should be ' . NodeInterface::TYPE_ARRAY);
            }
        }

        if ($type == NodeInterface::TYPE_INT) {
            if (!is_integer($data)) {
                throw new \Exception('Field: ' . $node->getFullName() . ' should be ' . NodeInterface::TYPE_INT);
            }
        }

        if ($type == NodeInterface::TYPE_FLOAT) {
            if (!is_float($data)) {
                throw new \Exception('Field: ' . $node->getFullName() . ' should be ' . NodeInterface::TYPE_FLOAT);
            }
        }

        if ($type == NodeInterface::TYPE_BOOL) {
            if (!is_bool($data)) {
                throw new \Exception('Field: ' . $node->getFullName() . ' should be ' . NodeInterface::TYPE_BOOL);
            }
        }
    }

    private function setOutputFields(NodeInterface $node, $value, &$result): void
    {
        if (!$node->isSet()) {
            return;
        }

        $outputFields = $node->getOutputFields();
        foreach ($outputFields as $outputField) {
            $result[$outputField] = $value;
        }
    }

    private function storeNodeState(NodeInterface $node): void
    {
        $this->state['name'] = $node->getFieldName();
        $this->state['is_set'] = $node->isSet();
    }

    private function restoreNodeState(NodeInterface $node): void
    {
        $node->setFieldName($this->state['name']);
        $node->isSet($this->state['is_set']);
    }

    private function getNotSetValue(NodeInterface $node): mixed
    {
        return match ($node->getFieldType()) {
            NodeInterface::TYPE_STRING => '',
            NodeInterface::TYPE_ARRAY => [],
            NodeInterface::TYPE_INT => 0,
            NodeInterface::TYPE_FLOAT => 0.0,
            NodeInterface::TYPE_BOOL, NodeInterface::TYPE_SCALAR => null
        };
    }
}