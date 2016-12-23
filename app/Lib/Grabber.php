<?php
namespace App\Lib;

use Symfony\Component\DomCrawler\Crawler;
use Curl\Curl;
use App\Lib\DomParser;
use App\Lib\Curler;
use Config;

class Grabber
{
    public function __construct(Curler $curler, DomParser $domParser)
    {
        $this->curler = $curler;
        $this->domParser = $domParser;
    }

    public function grab()
    {
        $curl = new Curl;
        $this->curler->setCurl($curl);

        $grabbed = Config::get('grabber');

        $value = [];
        foreach ($grabbed as $willGrab) {
            $response = $this->curler->curl($willGrab['url']);

            $crawler = new Crawler($response);
            $this->domParser->setCrawler($crawler);
            foreach ($willGrab['elements'] as $element) {
                $result = $this->domParser->getSiblings($element);

                $result = str_replace('.', '', $result);
                $result = str_replace(',', '.', $result);
                $value[] = (double) $result;
            }
        }

        return $value;
    }
}