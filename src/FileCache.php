<?php

namespace bemang\Cache;

use bemang\Cache\CacheInfo;
use Psr\SimpleCache\CacheInterface;

class FileCache
{
    protected $path;
    protected $cacheInfo;

    public function __construct(string $path)
    {
        $this->setPath($path);
        $this->initCacheInfo();
    }

    public function set($key, $value, $ttl = null)
    {
        if (is_string($key) and !empty($key)) {
            $newKeysId = $this->getCacheInfo()->getKeysId();
            $id = uniqid();
            $newKeysId[$key]['id'] = $id;
            file_put_contents($this->getPath() . '/cacheInfo', serialize($newKeysId));
            file_put_contents($this->getPath() . "/$id", serialize($value));
        } else {
            throw new InvalidArgumentException("La clé est invalide");
        }
    }

    public function get($key)
    {
        $id = $this->getCacheInfo()->getKeysId();
    }

    private function setPath(string $path)
    {
        if (is_dir($path)) {
            $this->path = $path;
        } else {
            throw new \Exception('Le chemin du cache est invalide');
        }
    }

    public function getPath()
    {
        return $this->path;
    }

    private function setCacheInfo(CacheInfo $cacheInfo)
    {
        $this->cacheInfo = $cacheInfo;
    }

    private function getCacheInfo()
    {
        return $this->cacheInfo;
    }

    private function initCacheInfo()
    {
        if (!file_exists($this->getPath() . '/cacheInfo')) {
            file_put_contents($this->getPath() . '/cacheInfo', serialize([]));
        }
        $cacheInfo = new CacheInfo(unserialize(file_get_contents($this->getPath() . '/cacheInfo')));
        $this->setCacheInfo($cacheInfo);
    }
}
