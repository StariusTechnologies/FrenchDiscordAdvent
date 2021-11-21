<?php

use Befew\Db;

const DEBUG = true;
const CACHE_TWIG_FOLDER = 'cache' . DIRECTORY_SEPARATOR . 'twig';
const STYLES_FOLDER = 'css';
const TEMPLATES_FOLDER = 'twig';
const SCRIPTS_FOLDER = 'js';

if (DEBUG) {
    error_reporting(-1);
    ini_set('display_errors', 1);

    if (DB_ACTIVE) {
        Db::getInstance()->setDebugMode(true);
    }
} else {
    error_reporting(0);
    ini_set('display_errors', 0);

    if (DB_ACTIVE) {
        Db::getInstance()->setDebugMode(false);
    }
}
