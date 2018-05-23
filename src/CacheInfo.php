<?php

namespace bemang\Cache;

class CacheInfo
{
    protected $keysId;

    public function __construct(array $keysId)
    {
        $this->setKeyId($keysId);
    }

    private function setKeyId(array $array)
    {
        if (!empty($array)) {
            $this->keysId = $array;
        }
    }

    public function getId(string $key)
    {
    }

    public function hasKey(string $key)
    {
    }

    public function getKeysId()
    {
        return $this->keysId;
    }
}
