<?php

namespace bemang\Cache;

class CacheInfo
{
    protected $keysId = [];

    public function __construct(array $keysId)
    {
        $this->setKeys($keysId);
    }

    public function setKeys(array $array)
    {
        if (!empty($array)) {
            $this->keysId = $array;
        }
    }

    public function getIdOfKey(string $key)
    {
        if ($this->hasKey($key)) {
            return $this->getKeysId()[$key]['id'];
        } else {
            return false;
        }
    }

    public function getKey(string $key)
    {
        if ($this->hasKey($key)) {
            return $this->getKeysId()[$key];
        } else {
            return false;
        }
    }

    public function hasKey(string $key)
    {
        if (isset($this->getKeysId()[$key]) && !empty($this->getKeysId()[$key])) {
            return true;
        } else {
            return false;
        }
    }

    public function getKeysId()
    {
        return $this->keysId;
    }
}
