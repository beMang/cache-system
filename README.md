# cache-system
Librarie php pour écrire du cache

[![Build Status](https://travis-ci.org/beMang/cache-system.svg?branch=master)](https://travis-ci.org/beMang/cache-system)   [![Coverage Status](https://coveralls.io/repos/github/beMang/cache-system/badge.svg?branch=master)](https://coveralls.io/github/beMang/cache-system?branch=master)

## Installation (composer)

```composer require```

## Usage

```php
    $cache = new \bemang\Cache\FileCache('pathToCache');
    $cache->set('keyName', 'value');
    $cache->get('keyName');
    $cache->has('keyName');
```

*Basé sur le psr16, pour plus d'infos : [CacheInterface](https://github.com/php-fig/simple-cache/blob/master/src/CacheInterface.php)*


Il est conseillé de vérifier la présence de clé avant d'écrire ou de récupérer des valeurs.