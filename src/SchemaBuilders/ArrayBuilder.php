<?php
declare(strict_types=1);

namespace DataLib\Transform\SchemaBuilders;

use DataLib\Transform\Interface\NodeInterface;
use DataLib\Transform\SchemaBuilder\ConfigTreeRoot;

class ArrayBuilder
{
    protected array $fullPath = [];

    public function build(ConfigTreeRoot $root, array $array = []): ConfigTreeRoot
    {
        foreach ($array as $key => $value) {
            $this->fullPath[] = $key;
            $type = $this->getType($value);
            $node = $root->field($key, $type);
            if ($type === NodeInterface::TYPE_ARRAY) {
                $child = $node->child();
                $this->build($child, $value);
                $node = $child->endChild();
            }
            $root = $node->end();
            array_pop($this->fullPath);
        }

        return $root;
    }

    protected function getType($value): string
    {
        if (is_string($value)) {
            return NodeInterface::TYPE_STRING;
        }

        if (is_array($value)) {
            return NodeInterface::TYPE_ARRAY;
        }

        if (is_integer($value)) {
            return NodeInterface::TYPE_INT;
        }

        if (is_float($value)) {
            return NodeInterface::TYPE_FLOAT;
        }

        if (is_bool($value)) {
            return NodeInterface::TYPE_BOOL;
        }

        if (is_scalar($value)) {
            return NodeInterface::TYPE_SCALAR;
        }

        if (is_null($value)) {
            return NodeInterface::TYPE_NULL;
        }

        throw new \Exception('Field: ' . $this->getCurrentKey() . ' \'' . gettype($value) . '\' type is not allowed');
    }

    protected function getCurrentKey()
    {
        return implode('.', $this->fullPath);
    }
}