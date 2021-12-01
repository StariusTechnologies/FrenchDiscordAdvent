<?php

use Befew\Db;

require 'vendor/autoload.php';

if(!class_exists('PDO')) {
    exit('FATAL ERROR: PDO isn\'t enabled on this server');
}

function autoloader($classname) {
    $classname = str_replace("_", "\\", $classname);
    $classname = ltrim($classname, '\\');
    $filename = '';
    $lastNsPos = strripos($classname, '\\');

    if ($lastNsPos !== false) {
        $namespace = substr($classname, 0, $lastNsPos);
        $firstNsPos = strpos($namespace, '\\');

        if ($firstNsPos !== false) {
            $firstFolder = substr($namespace, 0, $firstNsPos);
            $namespace = substr($namespace, $firstNsPos + 1);

            $namespace = ($firstFolder === 'Befew' ? 'lib' : 'src\\' . $firstFolder) . '\\' . $namespace;
        } else {
            $namespace = $namespace === 'Befew' ? 'lib' : 'src\\' . $namespace;
        }

        $classname = substr($classname, $lastNsPos + 1);
        $filename = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }

    $filename .= str_replace('_', DIRECTORY_SEPARATOR, $classname) . '.php';

    require preg_replace(
        '/^Befew' . preg_quote(DIRECTORY_SEPARATOR) . '/',
        'lib' . DIRECTORY_SEPARATOR,
        $filename
    );
}

spl_autoload_register('autoloader');
date_default_timezone_set('America/Los_Angeles');

if(defined('DB_NAME')) {
    define('DB_ACTIVE', true);
    Db::getInstance()->init();
} else {
    define('DB_ACTIVE', false);
}