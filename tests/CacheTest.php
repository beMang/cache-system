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
    }

    public function testConstruction()
    {
        self::$cacheInstance = new FileCache(self::CACHE_PATH);
        $this->assertInstanceOf(FileCache::class, self::$cacheInstance);
    }

    public function testInvalidsKeys()
    {
        $this->expectExceptionMessage('La clé est invalide');
        self::$cacheInstance->get([]);
        $this->expectExceptionMessage('La clé est invalide');
        self::$cacheInstance->set(1, 'failed');
        $this->expectExceptionMessage('La clé est invalide');
        self::$cacheInstance->delete('');
    }

    public function testGetAndSet()
    {
        self::$cacheInstance->setMultiple([
        'test1' => 'hello, test1',
        'test2' => 'hello, test2'
        ]);
        $this->assertEquals('hello, test1', self::$cacheInstance->get('test1'));
        self::$cacheInstance->set('test1', 'hello, redefine a key :)');
        $this->assertEquals('hello, redefine a key :)', self::$cacheInstance->get('test1'));
    }

    public function testHas()
    {
        $this->assertFalse(self::$cacheInstance->has(uniqid()));
        $this->assertTrue(self::$cacheInstance->has('test1'));
    }
}
