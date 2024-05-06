<?php
declare(strict_types=1);

namespace DataLib\Transform\SchemaBuilder;

class ConfigTreeRoot
{
    public function __construct(protected NodesManager $nodesManager)
    {
        $this->nodesManager->setConfigTreeRoot($this);
    }

    public function field(string $name, string $type): ConfigTreeField
    {
        return new ConfigTreeField($name, $type, $this->nodesManager);
    }
}