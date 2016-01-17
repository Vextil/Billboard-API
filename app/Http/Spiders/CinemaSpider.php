<?php

namespace App\Http\Spiders;

use Symfony\Component\DomCrawler\Crawler;
use Goutte\Client;

class CinemaSpider extends BaseSpider
{
	protected static $cacheName = 'cinemas';

	const TIPO_NORMAL = 'P';
	const TIPO_INFANTIL = 'I';

    protected static function fetchData(Client $client)
    {
    	return self::fetchBillboards($client, self::TIPO_NORMAL);
    }

	protected static function fetchBillboards(Client $client, $tipo)
	{
		// Get all cinema locatons IDs and Names
		$locations = [];
		$page = $client->request('GET', 'http://www.movie.com.uy/cine/');
		$page->filter('select#filter-complejo-cartelera > option')->each(
			function(Crawler $node) use (&$locations) {
				array_push(
					$locations,
					[
						'id' => $node->attr('value'),
						'name' => $node->text()
					]
				);
			}
		);

		// Get billboard for each cinema location
		foreach ($locations as &$location) {
			$location['billboard'] = self::fetchBillboard($client, $location['id'], $tipo);
		}

		return array_merge(SpiderHelper::getPosterData($page), ['locations' => $locations]);
	}

    private function fetchBillboard(Client $client, $id, $tipo)
    {
    	$page = $client->request(
    		'POST', 
    		'http://www.movie.com.uy/app/frontend/ajax/cartelera.php', 
    		[
    			'complejo_id' => $id,
    			'tipo' => $tipo,
    		]
    	);
    	return self::getMovies($page, true);
    }

	protected static function getMovies(Crawler $page, $utf8_decode)
	{
		$movies = [];
		$page->filter('.movie_module')->each(function (Crawler $node) use (&$movies, $utf8_decode) {
			$movie = [];
			$movie['id'] = $node->filter('div.hd > a.favourite')->attr('rel');
			$movie['name'] = $node->filter('div.hd > h3 > a')->text();
			// We need to decode the name ONLY if the data comes from movie.com.uy's AJAX endpoints
			// HTML coming from AJAX endpoints doesn't display accents correctly so it needs to be decoded
			if ($utf8_decode) {
				$movie['name'] = utf8_decode($movie['name']);
			}
			SpiderHelper::extractLanguageAnd3DFromTitle($movie['name'], $movie['language'], $movie['DDD']);
			if ($node->filter('div.estreno')->count()) {
				$movie['premiere'] = SpiderHelper::cleanDate($node->filter('div.estreno')->text());
			}
			$movie['rating'] = SpiderHelper::cleanRating($node->filter('div.rating > p')->text());
			$movie['poster'] = SpiderHelper::getPosterFromURL($node->filter('div.mg > img')->attr('src'));
			array_push($movies, $movie);
		});
		return $movies;
	}

}
