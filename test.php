<?php


require('vendor/autoload.php');

$cache = new \bemang\Cache\FileCache(__DIR__ . '/tmp/');
//$cache->set('hello', ['yoooooo', 'reyo'], 1440);
var_dump($cache->get('hello'));
