<?php

namespace Aishmurodov\MrocketSimpleIntegration\Helpers;

use Aishmurodov\MrocketSimpleIntegration\Interfaces\ConfigInterface;
use Aishmurodov\MrocketSimpleIntegration\Interfaces\ContactInfoConfigInterface;
use Aishmurodov\MrocketSimpleIntegration\Interfaces\WidgetConfigInterface;
use Exception;

class Config implements ConfigInterface {

    public static array $baseConfig = [];


    public function __construct (array $defaultConfig = [])
    {
        if (!empty($defaultConfig)) {
            self::$baseConfig = $defaultConfig;
        }
    }

    /**
     * @throws Exception
     */
    public function widget (): WidgetConfigInterface
    {

        if (!isset(self::$baseConfig['widget'])) {
            throw new Exception("You did not provided widget info");
        }
        return new WidgetConfig(self::$baseConfig['widget']);
    }

    /**
     * @throws Exception
     */
    public function contact (): ContactInfoConfigInterface
    {

        if (!isset(self::$baseConfig['contact'])) {
            throw new Exception("You did not provided contact info");
        }
        return new ContactInfoConfig(self::$baseConfig['contact']);
    }

}