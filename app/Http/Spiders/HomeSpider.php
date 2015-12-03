<?php

namespace App\Http\Spiders;

use Goutte\Client;

class HomeSpider extends BaseSpider
{
    protected static $cacheName = 'home';

    protected static function fetchData(Client $client)
    {
        return 'hi';
    }
}