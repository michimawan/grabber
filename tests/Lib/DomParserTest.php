<?php

use Symfony\Component\DomCrawler\Crawler;
use App\Lib\DomParser;

class DomParserTest extends BaseLibTest
{
    public function test_get()
    {
        $element = 'body';
        $crawler = Mockery::mock(Crawler::class)->makePartial();
        $crawler->shouldReceive('filter')
            ->with($element)
            ->once()
            ->andReturn('foo');

        $domParser = new DomParser;
        $domParser->setCrawler($crawler);
        $result = $domParser->get($element);
        $this->assertEquals('foo', $result);
    }

    public function test_getElementSiblings()
    {
        $element = 'body';
        $crawler = Mockery::mock(Crawler::class)->makePartial();
        $crawler->shouldReceive('filter')
            ->with($element)
            ->once()
            ->andReturn($crawler);
        $crawler->shouldReceive('siblings')
            ->once()
            ->andReturn('foo');

        $domParser = new DomParser;
        $domParser->setCrawler($crawler);
        $result = $domParser->getSiblings($element);
        $this->assertEquals('foo', $result);
    }
}
