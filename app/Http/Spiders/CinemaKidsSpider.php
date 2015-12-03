<?php

namespace App\Http\Spiders;

use Goutte\Client;

class CinemaKidsSpider extends CinemaSpider
{
    protected static $cacheName = 'kidscinemas';

    protected static function fetchData(Client $client)
    {
        return static::fetchBillboards($client, static::TIPO_INFANTIL);
    }
}