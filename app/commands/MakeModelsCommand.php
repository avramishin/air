<?php

class MakeModelsCommand extends AirCommand
{
    /**
     * @var string
     */
    private $classPrefix;

    function __construct($args = [])
    {
        $dbConfigName = "";
        if (!empty($args[0])) {
            $dbConfigName = $args[0];
        } else {
            echo "DB you want generate models for?\n";

            $mapping = [];
            $i = 1;
            /**
             * @var $cfg array
             */
            $cfg = cfg("mysql");

            foreach ($cfg as $name => $data) {
                $mapping[$i] = $name;
                echo "{$i}. {$name} for {$data['user']}@{$data['host']} db {$data['name']}\n";
                $i++;
            }

            if (empty($mapping)) {
                echo "No DB configuration found, check your configs in /config/mysql.php\n\n";
                exit();
            }

            while (true) {
                $number = readline("Enter number: ");
                if (isset($mapping[$number])) {
                    $dbConfigName = $mapping[$number];
                    break;
                }

                echo "Incorrect value {$number}, select number from the list above\n";
            }
        }

        $this->make($dbConfigName);
    }

    function make($dbConfigName)
    {
        $this->classPrefix = str_replace(' ', '', ucwords(str_replace('_', ' ', $dbConfigName)));
        $prefix = strtolower($this->classPrefix);

        $tableDefinitionClasses = [];
        $cfg = cfg("mysql", $dbConfigName);
        $tables = db($dbConfigName)->query("SHOW TABLES FROM `{$cfg['name']}`")->fetchAllArray();
        foreach ($tables as $tc) {
            $tableName = $tc[0];
            $this->console("Working on {$tableName}");
            $tableInfo = db($dbConfigName)->query("SHOW FULL COLUMNS FROM {$tableName}")->fetchAllAssoc();
            $tableClassName = $this->getClassByTable($tableName);

            $pk = [];


            foreach ($tableInfo as $k => $tableFields) {
                $tableInfo[$k]['PhpType'] = $this->getPhpType($tableInfo[$k]["Type"]);
                if ($tableFields['Key'] == 'PRI') {
                    $pk[] = sprintf("'%s'", $tableFields['Field']);
                }

                if (!empty($tableFields['Default']) && $tableFields['Default'] == "''") {
                    $tableInfo[$k]['Default'] = "";
                }
            }

            $tableDefinitionClasses[] = view("framework/db/table-class", [
                "tableName" => $tableName,
                "tableClassName" => $tableClassName,
                "dbConfigName" => $dbConfigName,
                "tableInfo" => $tableInfo,
                "methods" => []
            ]);

            $className = $this->getClassByTable($tableName);
            $entityClass = sprintf("%s/app/models/%s/%s.php", AIR_ROOT, $prefix, $className);

            if (!file_exists($entityClass)) {
                $entityClassContent = sprintf("<?php \n\n%s", view("framework/db/model-class", [
                    "tableName" => $tableName,
                    "dbConfigName" => $dbConfigName,
                    "tableClassName" => $tableClassName
                ]));

                $dir = dirname($entityClass);
                if (!file_exists($dir)) {
                    mkdir($dir, 0755, true);
                }

                file_put_contents($entityClass, $entityClassContent);
            }
        }

        if (count($tableDefinitionClasses)) {
            $data = sprintf("<?php \n\n%s", join("\n\n", $tableDefinitionClasses));
            $filename = sprintf("%s/app/models/%s/%sTableDefinitions.php", AIR_ROOT, $prefix, $dbConfigName);
            file_put_contents($filename, $data);
        }
    }

    private function getClassByTable($table)
    {
        $parts = explode('_', $table);
        foreach ($parts as $key => $value) {
            $parts[$key] = ucfirst($value);
        }
        return $this->classPrefix . join('', $parts);
    }

    private function getPhpType($mySqlType)
    {
        $types = [
            '/int\(\d+\)/' => "integer",
            '/varchar\(\d+\)/' => "string",
        ];

        foreach ($types as $re => $phpType) {
            if (preg_match($re, $mySqlType)) {
                return $phpType;
            }
        }

        return "string";
    }
}