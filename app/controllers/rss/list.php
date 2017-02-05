<?php

class RssListController extends Air\Controller
{
    function action()
    {
        $limit = $this->r('limit', 50);
        $viewMsgUrl = cfg()->baseurl . "/" . cfg()->errorHook->rss->view;
        $items = [];
        foreach ($this->getList() as $filename) {

            if (!$json = @file_get_contents($filename)) {
                continue;
            } elseif (!$message = @json_decode($json)) {
                continue;
            }

            $message->id = pathinfo($filename, PATHINFO_FILENAME);
            $message->body = $this->filterBody($message->body);
            $message->body = nl2br($message->body);
            $message->ts = date("r", strtotime($message->ts));
            $message->link = "{$viewMsgUrl}?id={$message->id}";
            $items[] = $message;

            if (count($items) >= $limit) {
                break;
            }
        }

        header('Content-Type: application/rss+xml; charset=UTF-8');
        echo $this->getTwig()->render("rss/list.twig", [
            'pubDate' => date("r"),
            'viewMsgUrl' => $viewMsgUrl,
            'items' => $items
        ]);
    }

    function filterBody($body)
    {
        return preg_replace('/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]' .
            '|[\x00-\x7F][\x80-\xBF]+' .
            '|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*' .
            '|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})' .
            '|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S',
            '?', $body);
    }

    function getList()
    {
        $dataDir = $this->getDataDir();
        $list = array();
        foreach (glob("{$dataDir}/*.json") as $filename) {
            $list[$filename] = filectime($filename);
        }
        arsort($list, SORT_NUMERIC);
        return array_keys($list);
    }

    function getDataDir()
    {
        $dataDir = ROOT . "/data/tmp/rss";
        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0755, true);
        }

        return $dataDir;
    }
}

new RssListController();