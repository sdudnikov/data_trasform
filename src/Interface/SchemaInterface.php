<?php
declare(strict_types=1);

namespace DataLib\Transform\Interface;

use DataLib\Transform\RootNode;

interface SchemaInterface
{
    public function getName(): string;

    public function getTree(): RootNode;

    public function transform(array $data): array;
}