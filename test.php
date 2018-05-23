<?php


require('vendor/autoload.php');

$cache = new \bemang\Cache\FileCache(__DIR__ . '/tmp');
$cache->set('hello', 'lol');
var_dump($cache);
