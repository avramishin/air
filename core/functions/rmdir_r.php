<?php
/**
 * Delete directory recursively
 * @param $dir
 * @return bool Operation success
 */
function rmdir_r($dir) {
    $dir = realpath($dir);
    if (!is_dir($dir) || is_link($dir)) return false;
    $files = glob("{$dir}/*");
    foreach ($files as $file) {
        if (is_dir($file)) {
            rmdir_r($file);
        } else {
            unlink($file);
        }
    }
    return rmdir($dir);
}