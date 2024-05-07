<?php
declare(strict_types=1);

namespace DataLib\Transform\Interface;

interface SchemaInterface
{
    public function getName(): string;

    public function getTree(): NodeInterface;
}