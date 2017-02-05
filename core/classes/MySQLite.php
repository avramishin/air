<?php

namespace Air;

class MySQLite
{
    /**
     * @var string
     */

    public $filename;
    /**
     * @var string
     */
    public $sql;

    /**
     * @var SQLite3
     */
    public $db;

    /**
     * @var SQLite3Result
     */
    public $result;

    function __construct($filename)
    {
        $this->filename = $filename;
        $this->db = new SQLite3($this->filename);
        $this->db->busyTimeout(15000);
    }

    function __destruct()
    {
        if ($this->db) {
            $this->db->close();
        }
    }

    /**
     * @return MySQLite
     */
    public function __invoke()
    {
        return $this;
    }

    /**
     * Magically append query with method name and text, replace placeholders
     * @param string $name
     * @param array $args
     * @return MySQLite
     */
    public function __call($name, $args)
    {
        $line = $name;
        $offset = 0;
        $words = array();
        while (preg_match('/([A-Za-z][a-z]*)(_*)/', $line, $m, PREG_OFFSET_CAPTURE, $offset)) {
            $words [] = strtoupper($m[1][0]);
            $offset = $m[0][1] + strlen($m[0][0]);
        }
        $args[0] = implode(' ', $words) . (isset($args[0]) ? (' ' . $args[0]) : '');
        return $this->queryArgs($args);
    }

    /**
     * Append query text and replace placeholders
     * @param string $text
     * @return MySQLite
     */
    public function q($text)
    {
        return $this->queryArgs(func_get_args());
    }

    /**
     * Append query text and replace placeholders
     * @param string $text
     * @return MySQLite
     */
    public function query($text)
    {
        return $this->queryArgs(func_get_args());
    }


    /**
     * Append query if condition is positive
     * @param mixed $condition
     * @param string $text
     * @return MySQLite
     */
    public function ifQ($condition, $text)
    {
        return call_user_func_array(array($this, 'ifQuery'), func_get_args());
    }

    /**
     * Append query if condition is positive
     * @param mixed $condition
     * @param string $text
     * @return MySQLite
     */
    public function ifQuery($condition, $text)
    {
        if ($condition) {
            $args = array_slice(func_get_args(), 1);
            return $this->queryArgs($args);
        }
        return $this;
    }

    /**
     * Append query text from array of arguments
     * @param array $args
     * @return MySQLite
     */
    protected function queryArgs(array $args)
    {
        if (count($args) > 1) {
            $this->sql .= $this->parse($args[0], array_slice($args, 1)) . "\n";
        } else {
            $this->sql .= $args[0] . "\n";
        }
        return $this;
    }

    /**
     * Replace placeholders with values
     * @param string $text
     * @param array $args
     * @return string
     */
    protected function parse($text, $args)
    {
        $i = 0;
        $me = $this;
        $text = preg_replace_callback('|#\?|', function () use (&$i, $args, $me) {
            return $me->quoteName($args[$i++]);
        }, $text);

        return preg_replace_callback('|\?|', function () use (&$i, $args, $me) {
            return $me->quote($args[$i++]);
        }, $text);
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
        return '"' . $name . '"';
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
        if (is_int($val)) return $val;

        if (is_array($val) || is_object($val)) {
            $values = array_map(array($this, 'quote'), (array)$val);
            return implode(', ', $values);
        }

        return "'" . $this->db->escapeString($val) . "'";
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
     * Create DELETE FROM operator
     * @param string table
     * @return MySQLite
     */
    public function deleteFrom($table)
    {
        $args = func_get_args();
        $args[0] = 'DELETE FROM ' . $table;
        return $this->queryArgs($args);
    }

    /**
     * Create WHERE operator
     * @param string $what
     * @return MySQLite
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
     * @return MySQLite
     */
    public function from($table)
    {
        $args = func_get_args();
        $args[0] = ' FROM ' . $table;
        return $this->queryArgs($args);
    }

    /**
     * Create SELECT operator
     * @param string $table
     * @return MySQLite
     */
    public function select($what = '*')
    {
        $args = func_get_args();
        $args[0] = ' SELECT ' . $what;
        return $this->queryArgs($args);
    }

    /**
     * Select all query
     * @return DalMysqlQuery
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
     * @return MySQLite
     */
    public function in($array)
    {
        return $this->query(sprintf(' IN(%s) ', $this->quoteIn($array)));
    }

    /**
     * @param $limit
     * @return MySQLite
     */
    public function limit($limit)
    {
        $args = func_get_args();
        $args[0] = ' LIMIT ' . $limit;
        return $this->queryArgs($args);
    }

    /**
     * @param $offset
     * @return MySQLite
     */
    public function offset($offset)
    {
        $args = func_get_args();
        $args[0] = ' OFFSET ' . $offset;
        return $this->queryArgs($args);
    }

    public function fetchObject($class = 'stdClass')
    {
        $this->exec();
        $array = $this->result->fetchArray(SQLITE3_ASSOC);
        $this->result->finalize();
        if ($array) {
            return $this->_createObject($array, $class);
        } else {
            return null;
        }
    }

    public function fetchOne()
    {

        if ($result = $this->fetchObject()) {
            foreach ($result as $key => $value) {
                return $value;
            }
        }

        return null;
    }

    public function fetchArray()
    {
        $this->exec();
        $row = $this->result->fetchArray(SQLITE3_ASSOC);
        $this->result->finalize();
        return $row;
    }

    public function fetchAllObject($class = 'stdClass')
    {
        $this->exec();
        $array = array();
        while ($row = $this->result->fetchArray(SQLITE3_ASSOC)) {
            $array[] = $this->_createObject($row, $class);
        }
        $this->result->finalize();
        return $array;
    }

    public function begin()
    {
        $this->q('BEGIN')->exec();
    }

    public function commit()
    {
        $this->q('COMMIT')->exec();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function fetchAllArray()
    {
        $this->exec();
        $array = array();
        while ($row = $this->result->fetchArray(SQLITE3_ASSOC)) {
            $array[] = $row;
        }
        $this->result->finalize();
        return $array;
    }

    public function fetchAllAssoc()
    {
        return $this->fetchAllArray();
    }

    public function fetchCell()
    {
        $this->exec();
        $row = $this->result->fetchArray(SQLITE3_NUM);
        $this->result->finalize();
        return isset($row[0]) ? $row[0] : null;
    }

    public function asSql()
    {
        return $this->sql;
    }

    public function affectedRows()
    {
        return $this->changes();
    }

    public function changes()
    {
        return $this->db->changes();
    }

    /**
     * Returns the row ID of the most recent INSERT into the database.
     * @return integer
     */
    public function lastId()
    {
        return $this->db->lastInsertRowID();
    }

    /**
     * @return MySQLite
     * @throws Exception
     */
    public function exec()
    {
        $sql = $this->sql;
        $this->sql = "";

        $this->result = @$this->db->query($sql);
        if ($this->db->lastErrorCode() != 0) {
            $message = sprintf("%s; SQL: {$sql}", $this->db->lastErrorMsg());
            throw new Exception($message, $this->db->lastErrorCode());
        }

        return $this;
    }

    /**
     * @param $table
     * @param $data
     * @return MySQLite
     */
    public function insertArray($table, $data)
    {
        $columns = $this->_buildColumns(array_keys($data));
        $values = $this->_buildValues(array_values($data));
        $this->sql = "INSERT INTO \"{$table}\" ({$columns}) VALUES ($values)";
        return $this;
    }


    /**
     * @param $table
     * @param $data
     * @return MySQLite
     */
    public function replaceArray($table, $data)
    {
        $columns = $this->_buildColumns(array_keys($data));
        $values = $this->_buildValues(array_values($data));
        $this->sql = "REPLACE INTO \"{$table}\" ({$columns}) VALUES ($values)";
        return $this;
    }

    public function vacuum()
    {
        $this->sql = "VACUUM";
        $this->exec();
    }

    /**
     * Build list of columns for INSERT or REPLACE
     * @param $array
     * @return string
     */
    protected function _buildColumns($array)
    {
        $columns = array();
        foreach ($array as $column) {
            $columns[] = $this->quoteName($column);
        }

        return join(", ", $columns);
    }

    /**
     * Build list of values for INSERT or REPLACE
     * @param $array
     * @return string
     */
    protected function _buildValues($array)
    {
        $values = array();
        foreach ($array as $value) {
            $values[] = $this->quote($value);
        }

        return join(", ", $values);
    }

    function _createObject($data, $class = 'stdCLass')
    {
        $object = new $class();
        foreach ($data as $key => $value) {
            $object->{$key} = $value;
        }

        return $object;
    }
}