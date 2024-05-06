<?php
declare(strict_types=1);

namespace DataLib\Transform;

final class RootNode extends Node
{
    static public function root(): self
    {
        return new self('root', 'root');
    }
}