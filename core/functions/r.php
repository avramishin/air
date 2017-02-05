<?php

/**
 * Get request value and trim
 * @param string $param
 * @param mixed $default
 * @return mixed
 */
function r($param, $default = '')
{
    if (isset($_REQUEST[$param]))
        return is_array($_REQUEST[$param]) ? $_REQUEST[$param] : trim($_REQUEST[$param]);
    return $default;
}