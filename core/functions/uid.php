<?php
/**
 * Generate random base32 string
 * @param int $len Length (optional)
 * @return string
 */
function uid($len = 24)
{
    $res = '';
    while (strlen($res) < $len) {
        $res .= base_convert(mt_rand(), 10, 32);
    }
    return substr($res, 0, $len);
}