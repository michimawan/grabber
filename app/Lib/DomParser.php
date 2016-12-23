<?php
namespace App\Lib;

use Symfony\Component\DomCrawler\Crawler;

class DomParser
{
    private $crawler;

    public function getCrawler()
    {
        return $this->crawler;
    }

    public function setCrawler(Crawler $crawler)
    {
        $this->crawler = $crawler;
    }

    public function get(string $element)
    {
        return $this->crawler->filter($element);
    }

    public function getSiblings(string $element)
    {
        return $this->crawler->filter($element)->html();
    }
}
