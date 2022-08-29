<?php

namespace Aishmurodov\MrocketSimpleIntegration\Helpers;

use Aishmurodov\MrocketSimpleIntegration\Interfaces\WidgetConfigInterface;
use Exception;

class WidgetConfig implements WidgetConfigInterface {

    private array $widget;

    /**
     * @throws Exception
     */
    public function __construct($widget) {
        $this->widget = $widget;
        $this->validate();
    }

    /**
     * @throws Exception
     */
    private function validate(): void
    {
        if (!isset($this->widget['id'])) {
            throw new Exception("Provide widget ID");
        }
        if (!isset($this->widget['secret'])) {
            throw new Exception("Provide widget SECRET");
        }
        if (!isset($this->widget['url'])) {
            throw new Exception("Provide widget URL");
        }
        if (!isset($this->widget['state'])) {
            throw new Exception("Provide widget STATE");
        }
        if (!isset($this->widget['subdomain'])) {
            throw new Exception("Provide widget SUBDOMAIN");
        }
    }

    public function getId(): string
    {
        return $this->widget['id'];
    }

    public function getSecret(): string
    {
        return $this->widget['secret'];
    }

    public function getUrl(): string
    {
        return $this->widget['url'];
    }

    public function getSubDomain(): string
    {
        return $this->widget['subdomain'];
    }

    public function getState(): string
    {
        return $this->widget['state'];
    }

}
