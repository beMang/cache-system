<?php

require('vendor/autoload.php');

$cache = new \bemang\Cache\FileCache(__DIR__ . '/tmp/');
$cache->set('hello', ['yoooooo', 'reyo'], 1);
$cache->set('yoo', 'salut');
var_dump($cache->deleteMultiple(['hello', 'yoo']));
var_dump($cache->getMultiple(['hello', 'yoo', 'kjm']));
