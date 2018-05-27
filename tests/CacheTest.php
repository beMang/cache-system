<?php

namespace Tests;

use bemang\Cache\FileCache;

class CacheTest extends \PHPUnit\Framework\TestCase
{
    const CACHE_PATH = __DIR__ . '/../tmp/';
    protected static $cacheInstance;

    public static function setUpBeforeClass()
    {
        require_once(__DIR__ . '/../vendor/autoload.php');
        self::$cacheInstance = new FileCache(self::CACHE_PATH);
    }


    public function testGetAndSet()
    {
        $cache = self::$cacheInstance;
        $cache->setMultiple([
        'test1' => 'hello, test1',
        'test2' => 'hello, test2'
        ]);
        $this->assertEquals('hello, test1', $cache->get('test1'));
    }
}
