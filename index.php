<?php

define ('MYBLOG', 1);

ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT | E_NOTICE);

spl_autoload_register(function ($class_name) 
{
    $filename = str_replace('_', DIRECTORY_SEPARATOR, strtolower($class_name)) . '.php';

    $file = __DIR__ . DIRECTORY_SEPARATOR . $filename;

    require_once $file;
});

require './classes/db.php';

session_start();

$app = new controller_base();

