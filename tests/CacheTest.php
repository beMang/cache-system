<?php

namespace Tests;

use bemang\Cache\FileCache;

class CacheTest extends \PHPUnit\Framework\TestCase
{
    const CACHE_PATH = __DIR__ . '/../tmp/';

    public function setUp()
    {
        require_once(__DIR__ . '/../vendor/autoload.php');
    }

    public function testGetAndSet()
    {
        $cache = new FileCache(self::CACHE_PATH);
        $cache->setMultiple([
        'test1' => 'hello, test1',
        'test2' => 'hello, test2'
        ]);
        $this->assertEquals('hello, test1', $cache->get('test1'));
    }
}
