<?php

namespace App\Http\Spiders;

use Symfony\Component\DomCrawler\Crawler;
use Goutte\Client;

class HomeSpider extends BaseSpider
{
    protected static $cacheName = 'home';

    protected static function fetchData(Client $client)
    {
        $page = $client->request('GET', 'http://www.movie.com.uy/');
        $categories = [];
        array_push($categories, self::createCategory('Cartelera de la Semana', $page->filter('div.proxima-semana')));
        array_push($categories, self::createCategory('Proximos Estrenos', $page->filter('div.proximos-estrenos')));
        return array_merge(SpiderHelper::getPosterData($page), ['categories' => $categories]);
    }

    private static function createCategory($name, Crawler $slider)
    {
        $items = [];
        $slider->filter('div.box_film')->each(function (Crawler $node) use (&$items) {
            $item = [];
            $item['name'] = $node->filter('div.mt > h3 > a')->text();
            $item['poster'] = SpiderHelper::getPosterFromURL($node->filter('div.mg > a > img')->attr('src'));
            $item['id'] = SpiderHelper::getIDFromPoster($item['poster']);
            SpiderHelper::extractLanguageAnd3DFromTitle($item['name'], $item['language'], $item['DDD']);
            array_push($items, $item);
        });
        return [
            'name' => $name,
            'items' => $items,
        ];
    }

}