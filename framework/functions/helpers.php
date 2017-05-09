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


/**
 * @param $path
 * @return string
 */
function app_path($path)
{
    return AIR_ROOT . '/app/' . $path;
}

/**
 * @param $path
 * @return string
 */
function storage_path($path)
{
    return AIR_ROOT . '/storage/' . $path;
}

/**
 * @param $path
 * @return string
 */
function public_path($path)
{
    return AIR_ROOT . '/public/' . $path;
}

/**
 * @param $path
 * @return string
 */
function view_path($path)
{
    return AIR_ROOT . '/app/views/' . $path;
}

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


/**
 * @param $path
 * @return string
 */
function config_path($path)
{
    return AIR_ROOT . '/config/' . $path;
}

/**
 * Get controller class name from absolute path
 * @param $filename
 * @return string
 */
function controller_class($filename)
{
    $replace = [
        AIR_ROOT . '/app/controllers' => '',
        '.php' => ''
    ];

    $relPath = str_replace(array_keys($replace), array_values($replace), $filename);

    $parts = [];
    foreach (explode('/', $relPath) as $value) {
        $parts[] = ucfirst($value);
    }

    $parts[] = "Controller";

    return join("", $parts);
}

/**
 * @param string $instance
 * @return AirMySqlQuery
 * @throws Exception
 */
function db($instance)
{
    static $db = [];

    if (!isset($db[$instance])) {

        if (!$connection = cfg('mysql', $instance, false)) {
            throw new Exception('Database config not found: ' . $instance);
        }
        $db[$instance] = new AirMySqlQuery($connection);
    }

    return $db[$instance]();
}

/**
 * Get fully qualified URL to the given path
 * @param $path
 * @param array $params optional url parameters
 * @return string
 */
function url($path, $params = [])
{
    $url = cfg('common', 'baseurl') . '/' . $path;
    if ($params) {
        $url .= "?" . http_build_query($params);
    }
    return $url;
}

/**
 * Delete directory recursively
 * @param $dir
 * @return bool Operation success
 */
function rmdir_r($dir)
{
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

/**
 * Get config parameter
 * @param string $config filename
 * @param $key string|boolean specify key to return or false to return entire config
 * @param string $default
 * @return string
 */
function cfg($config, $key = false, $default = '')
{
    static $cache;

    if (!isset($cache[$config])) {
        $filename = config_path($config . ".php");
        if (file_exists($filename)) {
            $cache[$config] = require $filename;
        } else {
            $cache[$config] = [];
        }
    }

    if ($key === false) {
        return $cache[$config];
    }

    if (!isset($cache[$config][$key])) {
        return $default;
    }

    return $cache[$config][$key];
}

/**
 * Check if string is JSON
 * @param $json
 * @return bool
 */
function is_json($json)
{
    return !preg_match('/[^,:{}\\[\\]0-9.\\-+Eaeflnr-u \\n\\r\\t]/',
        preg_replace('/"(\\.|[^"\\\\])*"/', '', $json));
}

/**
 * Escape SQL like special characters (escape character is \)
 * @param string $str
 * @return string
 */
function escape_like($str)
{
    return str_replace('_', '\\_', str_replace('%', '\\%', str_replace('\\', '\\\\', $str)));
}

/**
 * Render view with specified arguments
 * @param $name string template name
 * @param $args array
 * @throws Exception
 * @return string
 */
function view($name, $args = [])
{
    static $twig;

    $filename = view_path("{$name}.twig");
    if (!file_exists($filename)) {
        throw new Exception("TWIG view not found {$filename}");
    }

    if (!$twig) {
        $loader = new Twig_Loader_Filesystem(app_path("views"));
        $twig = new Twig_Environment($loader, [
            'cache' => storage_path("cache/twig"),
        ]);
    }

    return $twig->render("{$name}.twig", $args);
}