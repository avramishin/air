<?php
/**
 * Get associative array of objects
 * @param array $array Source array
 * @param string $field Field to use as key
 * @return array Result array
 */
function associate($array, $field)
{
    $res = array();
    foreach ($array as $r) {
        if (!empty($r->$field)) {
            $res[$r->$field] = $r;
        }
    }
    return $res;
}