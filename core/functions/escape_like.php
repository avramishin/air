<?php
/**
 * Escape SQL like special characters (escape character is \)
 * @param string $str
 * @return string
 */
function escape_like($str)
{
    return str_replace('_', '\\_', str_replace('%', '\\%', str_replace('\\', '\\\\', $str)));
}
