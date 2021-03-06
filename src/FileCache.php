<?php

namespace bemang\Cache;

use Psr\SimpleCache\CacheInterface;

/**
 * Class qui gère le cache avec des fichiers
 * @see CacheInterface
 */
class FileCache implements CacheInterface
{
    /**
     * Chemin du cache
     *
     * @var string
     */
    protected $path;

    /**
     * Contient la classe des infos du Cahe
     *
     * @var CacheInfo
     */
    protected $cacheInfo;

    /**
     * Constante pour le temps par défaut du cache
     * En minute (1440 minutes = 1 jour)
     */
    protected $defaultTtl = 1440;

    /**
     * Constructeur de FileCache
     *
     * @param string $path Chemin du cache
     */
    public function __construct(string $path)
    {
        $this->setPath($path);
        $this->updateCacheInfo();
    }

    public function set($key, $value, $ttl = null) : bool
    {
        $this->updateCacheInfo();
        if (is_null($ttl)) {
            $ttl = $this->getDefaultTtl();
        }
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
        $this->updateCacheInfo();
        if (is_string($key) and !empty($key)) {
            $keyName = $key;
            $key = $this->getCacheInfo()->getKey($key);
            if (is_array($key)) {
                if ($this->keyIsExpired($key) === true) {
                    $this->delete($keyName);
                    return $default;
                } else {
                    if (is_file($this->getPath() . $key['id'])) {
                        return unserialize(file_get_contents($this->getPath() . $key['id']));
                    } else {
                        return $default;
                    }
                }
            } else {
                return $default;
            }
        } else {
            throw new InvalidArgumentException("La clé est invalide");
            return $default;
        }
    }

    public function delete($key) : bool
    {
        $this->updateCacheInfo();
        if (is_string($key) and !empty($key)) {
            $keyName = $key;
            $key = $this->getCacheInfo()->getKey($key);
            if (is_array($key)) {
                $newKeysId = $this->getCacheInfo()->getKeysId();
                unset($newKeysId[$keyName]);
                $this->getCacheInfo()->setKeys($newKeysId);
                file_put_contents($this->getPath() . '/cacheInfo', serialize($newKeysId));
                if (is_file($this->getPath() . $key['id'])) {
                    unlink($this->getPath() . $key['id']);
                    return true;
                } else {
                    return true;
                }
            } else {
                return false;
            }
        } else {
            throw new InvalidArgumentException("La clé est invalide");
            return false;
        }
    }

    public function clear() : bool
    {
        $this->updateCacheInfo();
        $keys = $this->getCacheInfo()->getKeysId();
        foreach ($keys as $key) {
            if (is_file($this->getPath() . $key['id'])) {
                unlink($this->getPath() . $key['id']);
            }
        }
        $result = file_put_contents($this->getPath() . '/cacheInfo', serialize([]));
        if ($result == false) {
            return false;
        } else {
            return true;
        }
    }

    public function getMultiple($keys, $default = null) : array
    {
        if (is_iterable($keys)) {
            $resultKeys = [];
            foreach ($keys as $key) {
                if (is_string($key) and !empty($key)) {
                    $resultKeys[] = $this->get($key, $default);
                } else {
                    throw new InvalidArgumentException("Une des clés est invalide");
                }
            }
            return $resultKeys;
        } else {
            throw new InvalidArgumentException("La liste des clés invalide");
            return [];
        }
    }

    public function setMultiple($values, $ttl = null)
    {
        if (is_iterable($values)) {
            $result = true;
            foreach ($values as $key => $value) {
                if ($this->set($key, $value, $ttl) === false) {
                    $result = false;
                }
            }
            return $result;
        } else {
            throw new InvalidArgumentException("La liste des clés et des valeurs sont invalides");
            return false;
        }
    }

    public function deleteMultiple($keys) : bool
    {
        if (is_iterable($keys)) {
            $result = true;
            foreach ($keys as $key) {
                if (is_string($key) and !empty($key)) {
                    if ($this->delete($key) === false) {
                        $result = false;
                    }
                } else {
                    throw new InvalidArgumentException("Une des clés est invalide");
                }
            }
            return $result;
        } else {
            throw new InvalidArgumentException("La liste des clés invalides");
            return false;
        }
    }

    public function has($key)
    {
        $this->updateCacheInfo();
        if (is_string($key) and !empty($key)) {
            $keyName = $key;
            $key = $this->getCacheInfo()->getKey($keyName);
            if ($key === false) {
                return false;
            } else {
                if ($this->keyIsExpired($key) === true) {
                    $this->delete($keyName);
                    return false;
                } else {
                    return true;
                }
            }
        } else {
            throw new InvalidArgumentException("La clé est invalide");
        }
    }

    private function setPath(string $path)
    {
        if (is_dir($path)) {
            $this->path = $path;
        } else {
            throw new InvalidArgumentException('Le chemin du cache est invalide');
        }
    }

    /**
     * Récupère le chemin du cache
     *
     * @return string Chemin du cache
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Modifie le ttl par défaut
     *
     * @param integer $defaultTtl Defaut ttl (En minute)
     * @return integer Retourne le ttl fourni
     */
    public function setDefaultTtl(int $defaultTtl) : int
    {
        $this->defaultTtl = $defaultTtl;
        return $defaultTtl;
    }

    /**
     * Retourne le ttl par défaut
     *
     * @return integer Ttl par défaut (en minute)
     */
    public function getDefaultTtl() : int
    {
        return $this->defaultTtl;
    }

    private function setCacheInfo(CacheInfo $cacheInfo)
    {
        $this->cacheInfo = $cacheInfo;
    }

    private function getCacheInfo()
    {
        $this->updateCacheInfo();
        return $this->cacheInfo;
    }

    private function updateCacheInfo()
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
            $autorizedInterval = $key['ttl'];
            $interval = Time::getMinuteOfDateInterval($interval);
            $autorizedInterval = Time::getMinuteOfDateInterval($autorizedInterval);
            if ($interval >= $autorizedInterval) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
}
