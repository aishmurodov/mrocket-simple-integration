<?php

namespace Aishmurodov\MrocketSimpleIntegration\Interfaces;

interface ConfigInterface {

    public function __construct (array $defaultConfig = []);
    public function widget (): WidgetConfigInterface;
    public function contact (): ContactInfoConfigInterface;

}