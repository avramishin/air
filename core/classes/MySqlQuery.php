<?php

namespace Air;

require_once __DIR__ . "/AbstractQuery.php";

/**
 * Database exception
 */
class DalException extends \Exception
{
}

class MySqlQuery extends AbstractQuery
{

    public $connection = null;
    public $result = null;

    /**
     * Construct query from configuration or another MySqlQuery object
     * @param object $init Configuration object (host, user, password, dbname) or MySqlQuery
     */
    public function __construct($init = null)
    {
        if (get_class($init) == 'Air\MySqlQuery') {
            $this->cfg = $init->cfg;
            $this->connection = $init->connection;
        } else {
            $this->cfg = $init;
            if ($init) {
                $this->connect();
            }
        }
    }

    /**
     * Connect to database
     * @param object $cfg Configuration object (host, user, password, dbname)
     * @return MySqlQuery
     * @throws DalException
     */
    public function connect($cfg = null)
    {
        if ($cfg) $this->cfg = $cfg;
        if ($this->connection) return $this;
        $this->connection = new \mysqli(
            $this->cfg->host,
            $this->cfg->user,
            $this->cfg->pass,
            $this->cfg->name
        );
        if (!$this->connection || $this->connection->connect_errno) {
            throw new DalException('Connection failed: ' . mysqli_connect_error());
        }
        $this->connection->set_charset('utf8');
        return $this;
    }

    /**
     * Create new query with same connection
     * @return MySqlQuery
     */
    public function __invoke()
    {
        $this2 = new MySqlQuery($this);
        return $this2;
    }

    /**
     * Disconnect from database
     */
    public function disconnect()
    {
        if ($this->connection) $this->connection->close();
    }

    /**
     * Select all query
     * @return MySqlQuery
     */
    public function selectFrom()
    {
        $args = func_get_args();
        $args[0] = 'SELECT * FROM ' . $args[0];
        return $this->queryArgs($args);
    }

    /**
     * Create SQL IN(...) operator
     * @param $array
     * @return MySqlQuery
     */
    public function in($array)
    {
        return $this->query(sprintf(' IN(%s) ', $this->quoteIn($array)));
    }

    /**
     * Quote array for IN(?)
     * @param array $array
     * @return string
     */
    public function quoteIn($array)
    {
        $result = array();
        foreach ($array as $val) {
            $result[] = $this->quote($val);
        }

        return join(', ', $result);
    }

    /**
     * Quote database value
     * @param mixed $val
     * @return string
     */
    public function quote($val)
    {
        if ($val === null) return 'NULL';
        if ($val === false) return '0';
        if ($val === true) return '1';
        if (is_int($val)) return (string)$val;
        if (is_array($val) || is_object($val)) {
            $values = array_map(array($this, 'quote'), (array)$val);
            return implode(', ', $values);
        }
        return "'" . $this->connection->real_escape_string($val) . "'";
    }

    /**
     * Create SQL NOT IN(...) operator
     * @param $array
     * @return MySqlQuery
     */
    public function notIn($array)
    {
        return $this->query(sprintf(' NOT IN(%s) ', $this->quoteIn($array)));

    }

    /**
     * Limit query
     * @param int $limit
     * @return MySqlQuery
     */
    public function limit($limit)
    {
        $args = func_get_args();
        return $this->query('LIMIT ' . (int)$limit . (isset($args[1]) ? (', ' . (int)$args[1]) : ''));
    }

    /**
     * Insert data from assoc array of object
     * @param string $table
     * @param array $data
     * @return MySqlQuery
     */
    public function insertArray($table, $data)
    {
        $this->insertInto($table);
        $q = array();
        foreach ($data as $k => $v) {
            $q[] = $this->quoteName($k) . '=' . $this->quote($v);
        }
        return $this->set(implode(', ', $q));
    }

    /**
     * Insert bulk data
     * @param string $table
     * @param array $data
     * @return MySqlQuery
     * @throws Exception
     *
     */
    public function insertBulk($table, $data)
    {
        $data = (array)$data;
        if (!isset($data[0])) {
            throw new Exception("Array should contain at least 1 element");
        }

        $this->insertInto($table);

        $fields = array();

        foreach (array_keys((array)$data[0]) as $field) {
            $fields[] = $this->quoteName($field);
        }

        $this->query(' (' . join(',', $fields) . ') VALUES ');

        $blocks = array();
        foreach ($data as $row) {
            $values = array();
            foreach ($row as $value) {
                $values[] = $this->quote($value);
            }
            $blocks[] = "(" . join(',', $values) . ")";
        }
        return $this->query(join(",", $blocks));
    }

    /**
     * Replace bulk data
     * @param string $table
     * @param array $data
     * @return MySqlQuery
     * @throws Exception
     */
    public function replaceBulk($table, $data)
    {
        if (!isset($data[0])) {
            throw new Exception("Array should contain at least 1 element");
        }

        $this->replaceInto($table);

        $fields = array();

        foreach (array_keys((array)$data[0]) as $field) {
            $fields[] = $this->quoteName($field);
        }

        $this->query(' (' . join(',', $fields) . ') VALUES ');

        $blocks = array();
        foreach ($data as $row) {
            $values = array();
            foreach ($row as $value) {
                $values[] = $this->quote($value);
            }
            $blocks[] = "(" . join(',', $values) . ")";
        }
        return $this->query(join(",", $blocks));
    }

    /**
     * Quote database name
     * @param string $name
     * @return string
     */
    public function quoteName($name)
    {
        if (is_array($name) || is_object($name)) {
            $names = array_map(array($this, 'quoteName'), (array)$name);
            return implode(', ', $names);
        }
        return '`' . $name . '`';
    }

    /**
     * Insert data from assoc array of object
     * @param string $table
     * @param array $data
     * @return MySqlQuery
     */
    public function replaceArray($table, $data)
    {
        $this->replace($table);
        $q = array();
        foreach ($data as $k => $v) {
            $q[] = $this->quoteName($k) . '=' . $this->quote($v);
        }
        return $this->set(implode(', ', $q));
    }

    /**
     * Gnereate set statement from an array of values
     * @param array $values
     * @return MySqlQuery
     */
    public function setValues($values)
    {
        $q = array();
        foreach ($values as $k => $v) {
            $q[] = $this->quoteName($k) . '=' . $this->quote($v);
        }
        return $this->set(implode(', ', $q));
    }

    public function getLock($name, $timeout = 0.1)
    {
        return (bool)$this->select('GET_LOCK(?, ?)', $name, $timeout)->fetchCell();
    }

    /**
     * Fetch single value from database
     * @return mixed
     */
    public function fetchCell()
    {
        $this->exec();
        $row = $this->result->fetch_row();
        return $row[0];
    }

    /**
     * Execute query
     * @param bool $last_id
     * @return MySqlQuery
     * @throws DalException
     */
    public function exec($last_id = false)
    {
        $sql = $this->sql;
        $this->result = @$this->connection->query($sql);

        if (!$this->result && substr_count($this->connection->error, 'gone away')) {
            $this->connection = null;
            $this->connect();
            $this->exec($last_id);
        }

        $this->sql = '';
        $this->classname = null;
        if (!$this->result) {
            throw new DalException(sprintf("MySQL ERROR: %s, SQL: %s", $this->connection->error, $sql),
                $this->connection->errno);
        }
        return $last_id ? $this->lastId() : $this->result;
    }

    /**
     * Get last inserted id
     * @return mixed
     */
    public function lastId()
    {
        return $this->connection->insert_id;
    }

    /**
     * Select query
     * @param string $what
     * @return MySqlQuery
     */
    public function select($what = '*')
    {
        $args = func_get_args();
        $args[0] = 'SELECT ' . $what;
        return $this->queryArgs($args);
    }

    public function releaseLock($name)
    {
        return (bool)$this->select('RELEASE_LOCK(?)', $name)->fetchCell();
    }

    /**
     * Get affected rows
     * @return int
     */
    public function affectedRows()
    {
        return $this->connection->affected_rows;
    }

    /**
     * Fetch single value from database
     * @return mixed
     */
    public function fetchOne()
    {
        return $this->fetchCell();
    }

    /**
     * Fetch all rows as array of objects
     * @param string $class Result type
     * @return array
     */
    public function fetchAllObject($class = null)
    {
        if (!$class) $class = $this->classname;
        $this->exec();
        $res = array();
        while ($row = $class ? $this->result->fetch_object($class)
            : $this->result->fetch_object()) {
            $res [] = $row;
        }
        return $res;
    }

    /**
     * Fetch all rows as array of associative arrays
     * @return array
     */
    public function fetchAllAssoc()
    {
        $this->exec();
        $res = array();
        while ($row = $this->result->fetch_assoc()) {
            $res [] = $row;
        };
        return $res;
    }

    /**
     * Fetch first result field from all rows as array
     * @return array
     */
    public function fetchColumn()
    {
        return array_map(function ($row) {
            return $row[0];
        }, $this->fetchAllArray());
    }

    /**
     * Fetch all rows as array of arrays
     * @return array
     */
    public function fetchAllArray()
    {
        $this->exec();
        $res = array();
        while ($row = $this->result->fetch_array()) {
            $res [] = $row;
        };
        return $res;
    }

    /**
     * Fetch row as object from database
     * @param string $table
     * @param string $field Field in where clause
     * @param string $value Value in where clause
     * @param string $class Result type
     * @return object
     */
    public function getObject($table, $field, $value, $class = null)
    {
        $this->select()->from($table)->where("$field = ?", $value);
        return $this->fetchObject($class);
    }

    /**
     * Create WHERE operator
     * @param string $what
     * @return MySqlQuery
     */
    public function where($condition)
    {
        $args = func_get_args();
        $args[0] = ' WHERE ' . $condition;
        return $this->queryArgs($args);
    }

    /**
     * Create FROM operator
     * @param string $table
     * @return MySqlQuery
     */
    public function from($table)
    {
        $args = func_get_args();
        $args[0] = ' FROM ' . $table;
        return $this->queryArgs($args);
    }

    /**
     * Fetch row as object
     * @param string $class Result type
     * @return object
     */
    public function fetchObject($class = null)
    {
        if (!$class) $class = $this->classname;
        $this->exec();
        $row = $class ? $this->result->fetch_object($class)
            : $this->result->fetch_object();
        return $row;
    }

    /**
     * Fetch row as array from database
     * @param string $table
     * @param string $field Field in where clause
     * @param string $value Value in where clause
     * @return array
     */
    public function getArray($table, $field, $value)
    {
        $this->select()->from($table)->where("{$field} = ?", $value);
        return $this->fetchArray();
    }

    // Useless methods

    /**
     * Fetch row as array
     * @return array
     */
    public function fetchArray()
    {
        $this->exec();
        $row = $this->result->fetch_row();
        return $row;
    }

    /**
     * Fetch row as associative array from database
     * @param string $table
     * @param string $field Field in where clause
     * @param string $value Value in where clause
     * @return array
     */
    public function getAssoc($table, $field, $value)
    {
        $this->select()->from($table)->where("{$field} = ?", $value);
        return $this->fetchAssoc();
    }

    /**
     * Fetch row as associative array
     * @return array
     */
    public function fetchAssoc()
    {
        $this->exec();
        $row = $this->result->fetch_assoc();
        return $row;
    }

    /**
     * Get found rows
     * @return mixed
     */
    public function foundRows()
    {
        return $this->select('FOUND_ROWS()')->fetchCell();
    }

    /**
     * Print current query
     * @return $this
     */
    public function printSql()
    {
        echo $this->asSql();
        return $this;
    }

}