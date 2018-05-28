<?php

namespace Tests;

use bemang\Cache\FileCache;

/**
 * Class de test pour la lib CacheInfo
 */
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

    public function testInvalidPath()
    {
        $this->expectExceptionMessage('Le chemin du cache est invalide');
        new FileCache(uniqid());
    }

    public function testInvalidKeyGet()
    {
        $this->expectExceptionMessage('La clé est invalide');
        self::$cacheInstance->get([]);
    }

    public function testInvalidKeySet()
    {
        $this->expectExceptionMessage('La clé est invalide');
        self::$cacheInstance->set(1, 'failed');
    }

    public function testInvalidSetTtl()
    {
        $this->expectExceptionMessage('L\'interval ttl est invalide');
        self::$cacheInstance->set('test5', 'hello, test5', 'hl');
    }

    public function testInvalidDelete()
    {
        $this->expectExceptionMessage('La clé est invalide');
        self::$cacheInstance->delete('');
    }

    public function testInvalidKeyHas()
    {
        $this->expectExceptionMessage('La clé est invalide');
        self::$cacheInstance->has([]);
    }

    public function testInvalidListDeleteMultiple()
    {
        $this->expectExceptionMessage('La liste des clés invalide');
        self::$cacheInstance->deleteMultiple('jkm');
    }

    public function testInvalidArraySetMultiple()
    {
        $this->expectExceptionMessage('La liste des clés et des valeurs sont invalides');
        self::$cacheInstance->setMultiple(123);
    }

    public function testOneInvalidKeyDeleteMultiple()
    {
        $this->expectExceptionMessage('Une des clés est invalide');
        self::$cacheInstance->deleteMultiple([
            'hello',
            []
        ]);
    }

    public function testInvalidListGetMultiple()
    {
        $this->expectExceptionMessage('La liste des clés invalide');
        self::$cacheInstance->getMultiple('jkm');
    }

    public function testOneInvalidKeyGetMultiple()
    {
        $this->expectExceptionMessage('Une des clés est invalide');
        self::$cacheInstance->getMultiple([
            'hello',
            []
        ]);
    }

    public function testGetAndSet()
    {
        self::$cacheInstance->setMultiple([
        'test1' => 'hello, test1',
        'test2' => 'hello, test2',
        'test3' => 'hello, test3'
        ]);
        self::$cacheInstance->set('test4', 'hello, test4');
        $this->assertEquals('hello, test1', self::$cacheInstance->get('test1'));
        self::$cacheInstance->set('test1', 'hello, redefine a key :)');
        $this->assertEquals('hello, redefine a key :)', self::$cacheInstance->get('test1'));
        $this->assertEquals('default value', self::$cacheInstance->get(uniqid(), 'default value'));
    }

    public function testHas()
    {
        $this->assertFalse(self::$cacheInstance->has(uniqid()));
        $this->assertTrue(self::$cacheInstance->has('test1'));
    }

    public function testGetMultiple()
    {
        $this->assertEquals([
            self::$cacheInstance->get('test1'),
            self::$cacheInstance->get('test2')
        ], self::$cacheInstance->getMultiple(['test1', 'test2']));
    }

    public function testDelete()
    {
        $this->assertFalse(self::$cacheInstance->delete(uniqid()));
        $this->assertTrue(self::$cacheInstance->delete('test3'));
        $this->assertEquals('default value', self::$cacheInstance->get('test3', 'default value'));
    }

    public function testDeleteMultiple()
    {
        $this->assertFalse(self::$cacheInstance->deleteMultiple([uniqid(), uniqid()]));
        $this->assertTrue(self::$cacheInstance->deleteMultiple(['test2', 'test1']));
        $this->assertEquals('default value', self::$cacheInstance->get('test2', 'default value'));
    }

    public function testClear()
    {
        $this->assertTrue(self::$cacheInstance->clear());
        $this->assertTrue(self::$cacheInstance->clear());
    }

    public function testSetDefaultTtl()
    {
        self::$cacheInstance->setDefaultTtl(1440);
        $this->assertEquals(1440, self::$cacheInstance->getDefaultTtl());
    }
}
