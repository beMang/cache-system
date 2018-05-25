<?php

namespace bemang\Cache;

use bemang\Cache\Time;
use bemang\Cache\CacheInfo;
use Psr\SimpleCache\CacheInterface;

class FileCache
{
    protected $path;
    protected $cacheInfo;
    const DEFAULT_TTL = 1440;

    public function __construct(string $path)
    {
        $this->setPath($path);
        $this->initCacheInfo();
    }

    public function set($key, $value, $ttl = null)
    {
        if (is_string($key) and !empty($key)) {
            $ttlInterval = Time::getValidInterval($ttl);
            if ($this->getCacheInfo()->hasKey($key)) {
                $id = $this->getCacheInfo()->getIdOfKey($key);
                $newKeysId = $this->getCacheInfo()->getKeysId();
                $newKeysId[$key]['ttl'] = $ttlInterval;
                $this->getCacheInfo()->setKeys($newKeysId);
                file_put_contents($this->getPath() . '/cacheInfo', serialize($newKeysId));
                file_put_contents($this->getPath() . "/$id", serialize($value));
                return true;
            } else {
                $newKeysId = $this->getCacheInfo()->getKeysId();
                $id = uniqid();
                $newKeysId[$key]['id'] = $id;
                $newKeysId[$key]['ttl'] = $ttlInterval;
                $this->getCacheInfo()->setKeys($newKeysId);
                file_put_contents($this->getPath() . '/cacheInfo', serialize($newKeysId));
                file_put_contents($this->getPath() . "/$id", serialize($value));
                return true;
            }
        } else {
            throw new InvalidArgumentException("La clé est invalide");
            return false;
        }
    }

    public function get($key, $default = null)
    {
        if (is_string($key) and !empty($key)) {
            $keyName = $key;
            $key = $this->getCacheInfo()->getKey($key);
            if (is_array($key)) {
                return $this->keyIsExpired($key);
            } else {
                return $default;
            }
        } else {
            throw new InvalidArgumentException("La clé est invalide");
            return $default;
        }
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

    private function keyIsExpired(array $key) : bool
    {
        if (file_exists($this->getPath() . $key['id'])) {
            $lastChangeTime = stat($this->getPath() . $key['id'])['mtime'];
            $lastChangeDate = new \DateTime;
            $lastChangeDate->setTimestamp($lastChangeTime);
            $actualDate = new \DateTime;
            $interval = $actualDate->diff($lastChangeDate);
            var_dump($interval);
            var_dump($key['ttl']);
            return true;
        } else {
            return true;
        }
    }
}
