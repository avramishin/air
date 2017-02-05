<?php
/**
 * Get text in A-Z a-z 0-9 characters range
 * @param string $str
 * @return string
 */
function azname($str)
{
    $str = unaccent($str);
    $str = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
    $str = str_replace('&', 'and', $str);
    return preg_replace('#[^A-Za-z0-9\.\-]#', '_', $str);
}