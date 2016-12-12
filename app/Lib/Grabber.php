<?php
namespace App\Lib;

use Symfony\Component\DomCrawler\Crawler;
use Curl\Curl;
use App\Lib\DomParser;
use App\Lib\Curler;
use Config;

class Grabber
{
    public function grab()
    {
        $curl = new Curl;

        $curler = new Curler;
        $curler->setCurl($curl);

        $response = $curler->curl();

        $crawler = new Crawler($response);
        $domParser = new DomParser;
        $domParser->setCrawler($crawler);
        $elements = $domParser->getSiblings(Config::get('grabber.element'));

        $value = 0;
        foreach ($elements as $idx => $element) {
            if ($idx == 0) {
                $value = $element->nodeValue;
            }
        }

        return $value;
    }
}