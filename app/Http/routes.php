<?php

use App\Http\Spiders as Spiders;

$app->get('/billboard/cinema/kids', function() { return Spiders\CinemaKidsSpider::getFromCache(); });
$app->get('/billboard/cinema/premiere', function() { return Spiders\CinemaPremiereSpider::getFromCache(); });
$app->get('/billboard/cinema', function() { return Spiders\CinemaSpider::getFromCache(); });
$app->get('/billboard/home', function() { return Spiders\HomeSpider::getFromCache(); });
$app->get('/billboard/function/{id}', function($id) { return Spiders\FunctionSpider::getFromCache($id); });
$app->get('/theatre', function() { return Spiders\TheatreSpider::getFromCache(); });

