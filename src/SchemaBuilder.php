<?php
declare(strict_types=1);

namespace DataLib\Transform;

use DataLib\Transform\SchemaBuilder\ConfigTreeRoot;
use DataLib\Transform\SchemaBuilder\NodesManager;
use DataLib\Transform\SchemaBuilders\ArrayBuilder;
use SplObjectStorage;

class SchemaBuilder
{
    static private ?SplObjectStorage $objectStorage = null;

    static public function root(): ConfigTreeRoot
    {
        $objectStorage = self::getSplObject();
        $nodesManager = self::newNodesManager();
        $configTreeRoot =  new ConfigTreeRoot($nodesManager);
        $objectStorage[$configTreeRoot] = $nodesManager;
        return $configTreeRoot;
    }

    static public function createFromRoot(string $name, ConfigTreeRoot $configTreeRoot): Schema
    {
        $objectStorage = self::getSplObject();
        if (!isset($objectStorage[$configTreeRoot])) {
            throw new \Exception('Variable $configTreeRoot is not initialize!');
        }

        /** @var NodesManager $nodesManager */
        $nodesManager = $objectStorage[$configTreeRoot];
        return new Schema($name, $nodesManager->getRootNode());
    }

    static public function createFromArray(string $name, array $array = []): Schema
    {
        $arrayBuilder = new ArrayBuilder();
        $root = $arrayBuilder->build(self::root(), $array);
        return self::createFromRoot($name, $root);
    }

    static private function newNodesManager(): NodesManager
    {
        return new NodesManager();
    }

    static private function getSplObject(): SplObjectStorage
    {
        if (is_null(self::$objectStorage)) {
            self::$objectStorage = new SplObjectStorage();
        }

        return self::$objectStorage;
    }

}