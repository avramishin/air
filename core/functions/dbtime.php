<?php

/**
 * Return time in database format (Y-m-d H:i:s)
 * @param mixed $time Integer time or string date or 0 (for current time)
 * @return string
 */
function dbtime($time = false)
{
    if (is_string($time)) {
        $time = strtotime($time);
    }
    return $time ? date('Y-m-d H:i:s', $time) : date('Y-m-d H:i:s');
}