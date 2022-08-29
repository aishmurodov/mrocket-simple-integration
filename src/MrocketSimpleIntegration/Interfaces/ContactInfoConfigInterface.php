<?php

namespace Aishmurodov\MrocketSimpleIntegration\Interfaces;

interface ContactInfoConfigInterface {

    public function __construct (array $config);
    public function getPhoneFieldId (): string;
    public function getEmailFieldId (): string;
    public function getDefaultName (): string;

}