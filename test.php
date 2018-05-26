<?php

require('vendor/autoload.php');

$cache = new \bemang\Cache\FileCache(__DIR__ . '/tmp/');
$cache->set('hello', ['yoooooo', 'reyo'], 1);
var_dump($cache->get('hello'));
$cache->clear();
var_dump($cache->get('hello', 'ça marche pas'));
