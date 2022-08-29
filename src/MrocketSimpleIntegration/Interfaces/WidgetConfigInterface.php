<?php

namespace Aishmurodov\MrocketSimpleIntegration\Interfaces;

interface WidgetConfigInterface {

    public function __construct (array $config);
    public function getId(): string;
    public function getSecret(): string;
    public function getUrl(): string;
    public function getSubDomain(): string;
    public function getState(): string;

}