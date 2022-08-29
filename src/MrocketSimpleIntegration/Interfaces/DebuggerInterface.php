<?php

namespace Aishmurodov\MrocketSimpleIntegration\Interfaces;

interface DebuggerInterface {
    public function getCurrentTime (): string;
    public function echo (string $type, $object): void;
    public function error ($object): void;
    public function warn ($object): void;
    public function info ($object): void;
}