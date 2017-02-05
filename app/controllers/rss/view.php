<?php

class RssViewController extends Air\Controller
{
    function action()
    {
        $dataDir = ROOT . "/data/tmp/rss";
        $id = $this->r('id');
        $filename = $dataDir . "/" . $id . ".json";
        $this->response = [];

        if (file_exists($filename)) {
            $json = file_get_contents($filename);
            $message = json_decode($json);
            $this->response = [
                $message->subject,
                $message->body,
                $message->ts
            ];

            header('Content-Type: text/plain');
            echo join("\n\n", $this->response);
        }
    }
}

new RssViewController();