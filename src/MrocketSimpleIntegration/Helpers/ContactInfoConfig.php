<?php

namespace Aishmurodov\MrocketSimpleIntegration\Helpers;

use Aishmurodov\MrocketSimpleIntegration\Interfaces\ContactInfoConfigInterface;
use Exception;

class ContactInfoConfig implements ContactInfoConfigInterface {

    private array $config;

    /**
     * @throws Exception
     */
    public function __construct($config) {
        $this->config = $config;
        $this->validate();
    }

    /**
     * @throws Exception
     */
    private function validate(): void
    {
        if (!isset($this->config['phone_field'])) {
            throw new Exception("Provide contact phone_field");
        }
        if (!isset($this->config['email_field'])) {
            throw new Exception("Provide contact email_field");
        }
        if (!isset($this->config['default_name'])) {
            throw new Exception("Provide contact default_name");
        }
    }

    public function getPhoneFieldId (): string {
        return $this->config['phone_field'];
    }

    public function getEmailFieldId (): string {
        return $this->config['email_field'];
    }

    public function getDefaultName (): string {
        return $this->config['default_name'];
    }

}