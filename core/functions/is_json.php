<?php
/**
 * Check if string is JSON
 * @param $json
 * @return bool
 */
function is_json($json)
{
    return !preg_match('/[^,:{}\\[\\]0-9.\\-+Eaeflnr-u \\n\\r\\t]/',
        preg_replace('/"(\\.|[^"\\\\])*"/', '', $json));
}