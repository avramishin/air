<?php
namespace Air;

class Event
{
    function __construct($name, $args)
    {
        $dir = ROOT . "/app/events/{$name}";
        if (!is_dir($dir)) {
            return;
        }

        foreach (glob("{$dir}/*.php") as $_airEventHandler) {
            $this->execHandler($_airEventHandler, $args);
        }
    }

    function execHandler($_airEventHandler, $args)
    {
        extract($args);
        require $_airEventHandler;
    }
}