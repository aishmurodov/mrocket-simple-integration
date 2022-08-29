<?php

namespace Aishmurodov\MrocketSimpleIntegration\Interfaces;

interface PhoneNormalizerInterface {
    public static function normalizePhone (string $phone): string;
}