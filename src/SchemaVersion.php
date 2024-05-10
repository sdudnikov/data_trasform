<?php
declare(strict_types=1);

namespace DataLib\Transform;

use DataLib\Transform\Interface\NodeInterface;
use DataLib\Transform\SchemaBuilder\ConfigTreeRoot;

class SchemaVersion
{
    private array $json = [];

    static public function create(): self
    {
        return new self();
    }

    public function getSchemaVersion(Schema $schema, string $version): string
    {
        $this->json['version'] = $version;
        $this->json['name'] = $schema->getName();
        $this->json['children'] = [];
        $tree = $schema->getTree();

        foreach ($tree->getChildren() as $child) {
            $this->json['children'][$child->getFieldName()] = $this->prepareNode($child);
        }

        return json_encode($this->json, JSON_PRETTY_PRINT);
    }

    private function prepareNode(NodeInterface $node): array
    {
        $filedType = $node->getFieldType();
        $result = [
            'type' => $filedType
        ];

        foreach ($node->getChildren() as $child) {
            $result['children'][$child->getFieldName()] = $this->prepareNode($child);
        }

        return $result;
    }

    public function getSchemaByVersion(string $version): Schema
    {
        $version = json_decode($version, true);
        $schemaName = $version['name'] ?? null;
        $children = $version['children'] ?? [];
        if (!$schemaName) {
            throw new \Exception('Schema name not found!');
        }

        $root = SchemaBuilder::root();
        $this->build($root, $children);

        return SchemaBuilder::createFromRoot($schemaName, $root);
    }

    private function build(ConfigTreeRoot $root, array $nodes = []): void
    {
        foreach ($nodes as $key => $data) {
            $type = $data['type'] ?? null;
            $children = $data['children'] ?? [];
            if (!$type) {
                throw new \Exception('Field: ' . $key . ' Type is not set!');
            }

            $root = $root->field($key, $type);
            if ($children) {
                $childRoot = $root->child();
                $this->build($childRoot, $children);
                $root = $childRoot->endChild();
            }

            $root = $root->end();
        }
    }
}