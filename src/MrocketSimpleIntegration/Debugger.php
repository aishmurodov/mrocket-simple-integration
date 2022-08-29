<?php

namespace Aishmurodov\MrocketSimpleIntegration;

use Aishmurodov\MrocketSimpleIntegration\Interfaces\DebuggerInterface;
use Exception;

class Debugger implements DebuggerInterface {

    private bool $headerSet = false;
    private string $logsPath;

    public function __construct (string $defaultLogsPath = __DIR__ . "/logs")
    {
        $this->logsPath = $defaultLogsPath;
    }

    private function setHeaders (): void
    {
        if (!$this->headerSet) {
            header('Content-Type: application/json; charset=utf-8');
            $this->headerSet = true;
        }
    }

    private function isLogsFolderExist (): bool
    {
        return file_exists($this->logsPath);
    }

    private function createLogsFolder (): void
    {
        mkdir($this->logsPath);
    }

    private function saveLogFile (string $type, $toLog): void
    {
        if (!$this->isLogsFolderExist()) {
            $this->createLogsFolder();
        }

        file_put_contents($this->logsPath . "/" . $type . "-" . $this->getCurrentTime() . ".json", $toLog);
    }

    public function getCurrentTime (): string
    {
        return date("H:i:s-d.m.Y");
    }

    public function echo (string $type, $object): void
    {
        $this->setHeaders();

        $toLog = json_encode($object, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

        $this->saveLogFile($type, $toLog);

        exit($toLog);
    }

    public function error ($object): void
    {
        $this->echo("error", $object);
    }

    public function warn ($object): void
    {
        $this->echo("warn", $object);
    }

    public function info ($object): void
    {
        $this->echo("info", $object);
    }
}