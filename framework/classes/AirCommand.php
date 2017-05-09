<?php

class AirCommand
{
    const COLORS = [
        "red" => "\e[1;31m",
        "green" => "\e[1;32m",
        "default" => ""
    ];

    /**
     * @var AirLog
     */
    private $_logger;

    /**
     * Write line to log
     * @param $message string
     */
    protected function log($message)
    {
        $class = static::class;
        $filename = storage_path("logs/command/{$class}.log");

        if (!($this->_logger instanceof AirLog)) {
            $this->_logger = new AirLog($filename);
        }

        $this->_logger->writeLn($message);
    }

    /**
     * Log message to console
     * @param $msg
     * @param string $color
     */
    function console($msg, $color = "default")
    {
        $colorCode = self::COLORS[$color];
        $resetAttributes = "\e[1;0m";
        echo date('Y-m-d H:i:s') . ": {$colorCode}{$msg}{$resetAttributes}\n";
    }
}