<?php

class RssSubmitController extends Air\Controller
{
    public $dataDir;
    public $subject;
    public $body;

    function action()
    {

        $this->subject = $this->r('subject');
        $this->body = $this->r('body');
        $this->dataDir = $this->getDataDir();
        $response = $this->jsonResponse();

        try {

            if (!$this->subject && !$this->body) {
                throw new Exception('Empty subject or body');
            }

            $this->save();
            $this->gc();

        } catch (Exception $e) {
            $response->error($e->getMessage());
        }
    }

    function save()
    {
        $filename = $this->dataDir . '/' . date('YmdHis') . '-' . uniqid() . '.json';
        $data = [
            'subject' => $this->subject,
            'body' => $this->body,
            'ts' => date('Y-m-d H:i:s')
        ];

        @file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
        @chmod($filename, 0766);
    }

    function getDataDir()
    {
        $dataDir = ROOT . "/data/tmp/rss";
        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0755, true);
        }

        return $dataDir;
    }

    /**
     * Remove old files from dataDir
     */
    function gc()
    {
        if (!mt_rand(0, 100) > 80) {
            return;
        }

        $expireTime = time() - (1 * 24 * 3600);
        foreach (glob("{$this->dataDir}/*.json") as $filename) {
            if (filectime($filename) < $expireTime) {
                @unlink($filename);
            }
        }
    }
}

new RssSubmitController();