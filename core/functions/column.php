<?php
/**
 * Get array of fields from array of objects
 * @param array $array
 * @param string $field
 * @return array
 */
function column($array, $field)
{
    $res = array();
    foreach ($array as $r) {
        if (!empty($r->$field)) {
            $res [] = $r->$field;
        }
    }
    return $res;
}