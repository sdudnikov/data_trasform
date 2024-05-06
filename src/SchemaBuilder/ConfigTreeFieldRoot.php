<?php
declare(strict_types=1);

namespace DataLib\Transform\SchemaBuilder;

class ConfigTreeFieldRoot extends ConfigTreeRoot
{
    public function __construct(
        NodesManager $nodesManager,
        protected ConfigTreeField $parentField
    ) {
        parent::__construct($nodesManager);
    }

    public function endChild(): ConfigTreeField
    {
        return $this->parentField;
    }
}