<?php
/**
 * User: Hamed Tahmooresi
 * Date: 2/9/2016
 * Time: 9:45 AM
 */
define('_OW_', true);
define('DS', DIRECTORY_SEPARATOR);
define('OW_DIR_ROOT', dirname(__FILE__).DS.'..'.DS.'..'.DS);
define('OW_CRON', true);

require_once(OW_DIR_ROOT . 'ow_includes' . DS . 'init.php');

return array(
    "paths" => [
        "migrations" => dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'phinx'.DIRECTORY_SEPARATOR.'migrations'
    ],
    "environments" => [
        "default_migration_table" => "phinxlog",
        "default_database" => "dev",
        "dev" => [
            "adapter" => 'mysql',
            "host" => OW_DB_HOST,
            "name" => OW_DB_NAME,
            "user" => OW_DB_USER,
            "pass" => OW_DB_PASSWORD,
            "charset" => 'utf8',
        ]
    ]
);