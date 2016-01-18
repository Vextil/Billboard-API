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
		$function['poster'] = SpiderHelper::getPosterFromURL($page->filter('div.mg > img')->attr('src'));
		$function['rating'] = SpiderHelper::cleanRating($page->filter('div.rating > p')->text());
		$info = $page->filter('div.film_info > p');
		$function['premiere'] = SpiderHelper::cleanInfo($info->eq(0)->text());
		$function['age_restriction'] = SpiderHelper::cleanInfo($info->eq(1)->text());
		$function['genre'] = SpiderHelper::cleanInfo($info->eq(2)->text());
		$function['duration'] = SpiderHelper::cleanInfo($info->eq(3)->text());
		$function['description'] = $page->filter('div.film_text > p')->text();
		$theatres = [];
		$page->filter('ul#tabs_complejos_horarios > li > a')->each(function (Crawler $node) use (&$theatres, $page) {
			$theatre = [];
			// href structure: #tabs-ID_number, this code isolates the ID
			$theatre['id'] = explode('-', explode('_', $node->attr('href'))[0])[1];
			$theatre['name'] = $node->text();
			$theatre['screenings'] = self::getScreenings($page, str_replace('#', '', $node->attr('href')));
			array_push($theatres, $theatre);
		});
		$function['theatres'] = $theatres;

		return $function;
	}

	private static function getScreenings(Crawler $page, $tabId) 
	{
		$screenings = [];
		$page = $page->filter('div#' . $tabId);
		$page->filter('div.shedule')->each(function (Crawler $node) use (&$screenings) {
			$screening = [];
			$screening['day'] = $node->filter('p')->text();
			$screening['hours'] = explode('-', str_replace(' ', '', $node->filter('span')->text()));
			array_push($screenings, $screening);
		});
		return $screenings;
	}

}