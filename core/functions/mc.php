<?php
/**
 * Get/set memcached value
 * @static var object $memcache
 * @param mixed $key
 * @param mixed $value if value is false then get value, else set value
 * @param int $timeout timeout in seconds
 * @return mixed value or false
 */
function mc($key, $value = false, $timeout = false)
{
    static $memcache = null;

    if (!cfg()->memcache->active) {
        return false;
    }

    if (!class_exists('Memcache')) {
        throw new Exception('No memcache - no fun');
    }

    if (!$memcache) {
        $memcache = new Memcache();
        if (!$memcache->connect(cfg()->memcache->host, cfg()->memcache->port)) {
            return '';
            # throw new RuntimeException('Memcache connect failed');
        }
    }

    if ($value === false) {
        return $memcache->get($key);
    } elseif ($value === null) {
        $memcache->delete($key, 0);
    } else {
        if (!$memcache->set($key, $value, false, $timeout ? $timeout : cfg()->memcache->timeout)) {
            # throw new RuntimeException('Memcache set failed');
        }
    }
}