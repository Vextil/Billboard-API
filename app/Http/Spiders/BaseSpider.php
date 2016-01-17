<?php

namespace App\Http\Spiders;

use Goutte\Client;
use Cache;

class BaseSpider
{
    protected static $cacheName = 'cache';
    protected static $cacheDuration;

    static function getFromCache()
    {
        if (!Cache::has(static::$cacheName)) {
            static::updateCache();
        }
        return Cache::get(static::$cacheName);
    }

    static function updateCache()
    {
        if (isset($cacheDuration)) {
            Cache::put(static::$cacheName, static::fetchData(new Client()), static::$cacheDuration);
        } else {
            Cache::forever(static::$cacheName, static::fetchData(new Client()));
        }
    }

    protected static function fetchData(Client $client)
    {
        return $client;
    }

}