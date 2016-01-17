<?php

namespace App\Http\Spiders;

use Symfony\Component\DomCrawler\Crawler;
use Goutte\Client;

class FunctionSpider extends BaseSpider
{

    protected static $cacheName = 'function';
    protected static $cacheDuration = 1440;

	static function getFromCache($id)
	{
		self::$cacheName = "function_" . $id;
		return parent::getFromCache();
	}

	protected static function fetchData(Client $client)
	{
		$id = explode('_', self::$cacheName)[1];
		$page = $client->request('GET', 'http://www.movie.com.uy/pelicula/' . $id . '_/');
		return array_merge(SpiderHelper::getPosterData($page), ['function' => self::getFunction($page)]);
	}

	private static function getFunction($page)
	{
		$function = [];
		$function['name'] = $page->filter('div#info > div.hd > h3')->text();
		SpiderHelper::extractLanguageAnd3DFromTitle($function['name'], $function['language'], $function['DDD']);
		$function['poster'] = SpiderHelper::getPosterFromURL($page->filter('div#info > div.mg > img')->attr('src'));
		$function['rating'] = SpiderHelper::cleanRating($page->filter('div.rating > p')->text());
		$info = $page->filter('div.film_info > p');
		$function['premiere'] = SpiderHelper::cleanInfo($info->eq(0)->text());
		$function['age_restriction'] = SpiderHelper::cleanInfo($info->eq(1)->text());
		$function['genre'] = SpiderHelper::cleanInfo($info->eq(2)->text());
		$function['duration'] = SpiderHelper::cleanInfo($info->eq(3)->text());
		return $function;
	}

}