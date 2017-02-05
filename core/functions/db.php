<?php
/**
 * @param string $instance
 * @return MySqlQuery
 * @throws Exception
 */
function db($instance)
{
    static $db = [];
    if (!isset($db[$instance])) {

        $dbs = cfg()->db;
        if (!isset($dbs->{$instance})) {
            throw new Exception("Database config not found: {$instance}");
        }

        $cfg = $dbs->{$instance};
        switch ($cfg->type) {
            case "mysql":
                $db[$instance] = new \Air\MySqlQuery($cfg);
                break;
            case "sqlite":
                $db[$instance] = new \Air\MySQLite($cfg->file);
                break;
        }
    }

    return $db[$instance]();
}