<?php
/**
 * Sends all notifications to a specified RSS feed.
 *
 * Consider using this class together with Debug_ErrorHook_RemoveDupsWrapper
 * to avoid network flooding when a lot of errors arrives.
 */

require_once __DIR__ . "/Util.php";
require_once __DIR__ . "/TextNotifier.php";

class Debug_ErrorHook_RssNotifier extends Debug_ErrorHook_TextNotifier
{

    protected $rssSubmitUrl;

    public function __construct($whatToSend, $rssSubmitUrl)
    {
        $this->rssSubmitUrl = $rssSubmitUrl;
        parent::__construct($whatToSend);
    }

    protected function _notifyText($subject, $body)
    {
        $args = http_build_query([
                'subject' => $subject,
                'body' => $body
            ]
        );

        @file_get_contents($this->rssSubmitUrl . '?' . $args);
    }
}
