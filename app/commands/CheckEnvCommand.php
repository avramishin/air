<?php

class CheckEnvCommand extends AirCommand
{

    private $pass = true;

    function __construct()
    {
        $this->console("Performing Environment Check", "green");
        $this->shortOpenTag();
        $this->checkMySQLLi();
        $this->storageExists();
        $this->storageWritable();

        if ($this->pass) {
            $this->console("Everything is OK!", "green");
        }
    }

    private function shortOpenTag()
    {
        if (ini_get("short_open_tag") != 1) {
            $this->showError("short_open_tag should be enabled");
        }
    }

    private function checkMySQLLi()
    {
        if (!class_exists('mysqli')) {
            $this->showError("mysqli not found");
        }
    }

    private function storageExists()
    {
        $storagePath = AIR_ROOT . "/storage";
        if (!is_dir($storagePath)) {
            $this->showError("storage dir does not exist {$storagePath}");
        }
    }

    private function storageWritable()
    {
        $storagePath = AIR_ROOT . "/storage";
        if (!is_writable($storagePath)) {
            $this->showError("storage dir not writable {$storagePath}");
        }
    }

    private function showError($message)
    {
        $this->pass = false;
        $this->console($message, "red");
    }
}