<?php

namespace App\Http\Spiders;

use Goutte\Client;

class CinemaPremiereSpider extends CinemaSpider
{
    protected static $cacheName = 'premierecinemas';

    protected static function fetchData(Client $client)
    {
        $page = $client->request('GET', 'http://www.movie.com.uy/cine/');
        $premieres = self::getMovies($page->filter('div#proximos_est'), false);
        return array_merge(SpiderHelper::getPosterData($page), ['billboard' => $premieres]);
    }
}