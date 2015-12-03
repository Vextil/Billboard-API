<?php

namespace App\Http\Spiders;

use Goutte\Client;

class PricesSpider extends BaseSpider
{
    protected static $cacheName = 'prices';

    protected static function fetchData(Client $client)
    {

    }
}