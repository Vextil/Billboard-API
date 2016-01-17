<?php

namespace app\Http\Spiders;

use Symfony\Component\DomCrawler\Crawler;

class SpiderHelper
{
    static function extractLanguageAnd3DFromTitle(&$title, &$language, &$threeD)
    {
        if (strpos($title, ' 3D Sub')) {
            $language = 'sub';
            $threeD = true;
        } else if (strpos($title, ' 3D Esp')) {
            $language = 'esp';
            $threeD = true;
        } else if (strpos($title, ' 2D Sub')) {
            $language = 'sub';
            $threeD = false;
        } else if (strpos($title, ' 2D Esp')) {
            $language = 'esp';
            $threeD = false;
        } else if (strpos($title, ' - Sub')) {
            $language = 'sub';
            $threeD = false;
        } else {
            $language = 'esp';
            $threeD = false;
        }
        $title = str_replace(array(' - 2D Esp', ' - 3D Esp', ' - Esp', ' - Sub', ' -', ' 3D Sub', ' 3D Esp', ' 2D Sub', ' 2D Esp'), '', $title);
    }

    static function cleanInfo($info)
    {
        return explode(': ', $info)[1];
    }

    static function cleanDate($date)
    {
        return str_replace(' | ', ' ', $date);
    }

    static function cleanRating($rating)
    {
        return str_replace(array('Rating ', '%'), '', $rating);
    }

    static function getPosterFromURL($URL)
    {
        return end(explode('/', $URL));
    }

    static function getIDFromPoster($poster)
    {
        return explode('_', $poster)[0];
    }

    static function getPosterData(Crawler $page)
    {
        return [
            'poster' => [
                'url' => 'https://d3jovt6qzgy13f.cloudfront.net/upload/peliculas/afiche/',
                'sizes' => [
                    'small' => '70x100',
                    'medium' => '147x210',
                    'big' => '216x306',
                ],
            ]
        ];
    }
}