<?php

class AirJsonResponse
{
    private $errors = [];
    private $data = [];

    function error($errors)
    {
        $this->errors = is_array($errors) ? $errors : [$errors];
        $this->send();
    }

    function setData($data)
    {
        $this->data = $data;
    }

    function send($exit = true)
    {
        header('Content-Type: application/json');
        echo json_encode($this);
        flush();
        if ($exit) {
            exit();
        }
    }
}