<?php

class AirEvent
{
    /**
     * @var AirLog
     */
    private $_logger;

    /**
     * Write line to log
     * @param $message string
     */
    function log($message)
    {
        $class = static::class;
        $filename = storage_path("logs/event/{$class}.log");

        if (!($this->_logger instanceof AirLog)) {
            $this->_logger = new AirLog($filename);
        }

        $this->_logger->writeLn($message);
    }
}