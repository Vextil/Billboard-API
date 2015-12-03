<?php

namespace App\Http\Spiders;

use Symfony\Component\DomCrawler\Crawler;
use Goutte\Client;

class TheatreSpider extends BaseSpider
{
    protected static $cacheName = 'theatres';

    protected static function fetchData(Client $client)
    {
        $page = $client->request('GET', 'http://www.movie.com.uy/teatro/');
        $premieres = self::getPlays($page->filter('div#proximos_est'));
        $billboard = self::getPlays($page->filter('div#cartelera_prin'));
        return array_merge(SpiderHelper::getPosterData($page), ['billboard' => array_merge($premieres, $billboard)]);
    }

    private function getPlays(Crawler $container)
    {
        $plays = [];
        $container->filter('.movie_module')->each(function (Crawler $node) use (&$plays) {
            $play = [];
            $play['id'] = $node->filter('div.hd > a.favourite')->attr('rel');
            $play['name'] = $node->filter('div.hd > h3 > a')->text();
            if ($node->filter('div.estreno')->count()) {
                $play['premiere'] = SpiderHelper::cleanDate($node->filter('div.estreno')->text());
            }
            $play['rating'] = SpiderHelper::cleanRating($node->filter('div.rating > p')->text());
            $play['poster'] = SpiderHelper::getPosterFromURL($node->filter('div.mg > img')->attr('src'));
            array_push($plays, $play);
        });
        return $plays;
    }

}
