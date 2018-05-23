<?php

namespace Tests;

class CacheTest
{
    public function myIdea()
    {
        $cache = new Cache($path);
        $cache->set('key', 'value');
        $cache->get('key');
    }
}
