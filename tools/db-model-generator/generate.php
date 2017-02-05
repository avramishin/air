!<?php

require __DIR__ . "/../../bootstrap.php";

$dbConfigName = '';
$classPrefix = '';

if (empty($argv[1])) {
    echo "For what DB you want generate models?\n";

    $mapping = [];
    $i = 1;

    foreach (cfg()->db as $name => $config) {
        if ($config->type == 'mysql') {
            $mapping[$i] = $name;
            echo "{$i}. {$name} for {$config->user}@{$config->host} db {$config->name}\n";
            $i++;
        }
    }

    if (empty($mapping)) {
        echo "No mysql DB configuration found, check your JSON configs in /app/conf/\n\n";
        exit();
    }

    $answer = 0;

    while (true) {
        $number = readline("Enter number: ");
        if (isset($mapping[$number])) {
            $argv[1] = $mapping[$number];
            break;
        }

        echo "Incorrect value {$number}, select number from the list above\n";
    }
}

if (@$argv[1]) {
    $dbConfigName = strtolower($argv[1]);
    $classPrefix = str_replace(' ', '', ucwords(str_replace('_', ' ', $dbConfigName)));
    echo "Using database config $dbConfigName, class prefix $classPrefix\n";
}

if (empty(cfg()->db->$dbConfigName)) {
    exit("Config $dbConfigName not found\n");
}

$dbConfig = cfg()->db->$dbConfigName;
if ($dbConfig->type != 'mysql') {
    exit("this tool can generate classes only for mysql db\n");
}

$tableDefinitionClasses = [];
$tables = db($dbConfigName)->query("SHOW TABLES FROM `{$dbConfig->name}`")->fetchAllArray();
foreach ($tables as $tc) {
    $tableName = $tc[0];
    echo "Working on {$tableName}\n";
    $tableInfo = db($dbConfigName)->query("SHOW FULL COLUMNS FROM {$tableName}")->fetchAllAssoc();
    $tableClassName = getClassByTable($tableName);

    $pk = array();
    $methods = array();
    $indexes = array();
    foreach ($tableInfo as $k => $tableFields) {
        if ($tableFields['Key'] == 'PRI') {
            $pk[] = sprintf("'%s'", $tableFields['Field']);
        }

        if (!empty($tableFields['Default']) && $tableFields['Default'] == "''") {
            $tableInfo[$k]['Default'] = "";
        }

        $comment = trim($tableFields['Comment']);
        if (preg_match('#^(.+)\((.+)\.(.+)\)$#', $comment, $m)) {
            print_r($m);
            ob_start();
            $method = array(
                'foreignClass' => getClassByTable($m[2]),
                'foreignTable' => $m[2],
                'foreignField' => $m[3],
                'name' => $m[1],
                'localTable' => $tableName,
                'localField' => $tableFields['Field']
            );

            require __DIR__ . "/templates/table-method-get-one.tpl";
            $methods[] = ob_get_contents();
            ob_end_clean();
        }
    }

    $prefix = strtolower($classPrefix);
    ob_start();
    require __DIR__ . "/templates/table-class.tpl";
    $tableDefinitionClasses[] = ob_get_contents();
    ob_end_clean();
    $className = getClassByTable($tableName);
    $entityClass = sprintf("%s/app/models/%s/%s.php", ROOT, $prefix, $className);

    if (!file_exists($entityClass)) {
        ob_start();
        require __DIR__ . "/templates/entity-class.tpl";
        $entityClassContent = sprintf("<?php \n\n%s", ob_get_contents());

        $dir = dirname($entityClass);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($entityClass, $entityClassContent);
        ob_end_clean();
    }
}

if (count($tableDefinitionClasses)) {
    $data = sprintf("<?php \n\n%s", join("\n\n", $tableDefinitionClasses));
    $filename = sprintf("%s/app/models/%s/TableDefinitions.php", ROOT, $prefix);
    file_put_contents($filename, $data);
}

function getClassByTable($table)
{
    global $classPrefix;
    $parts = explode('_', $table);
    foreach ($parts as $key => $value) {
        $parts[$key] = ucfirst($value);
    }
    return $classPrefix . join('', $parts);
}