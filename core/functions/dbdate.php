<?php

/**
 * Return date in database format (Y-m-d)
 * @param mixed $time Integer time or string date or 0 (for current date)
 * @return string
 */
function dbdate($time = false)
{
    if (is_string($time)) {
        $time = strtotime($time);
    }
    return $time ? date('Y-m-d', $time) : date('Y-m-d');
}
