<?php

use App\Lib\Curler;
use App\Lib\Grabber;
use App\Lib\DomParser;

class GrabberTest extends BaseLibTest
{
    public function setUp()
    {
        parent::setUp();
        $this->curler = Mockery::mock(Curler::class)->makePartial();
        $this->domParser = Mockery::mock(DomParser::class)->makePartial();

        $this->curler->shouldReceive('setCurl')->once();
    }

    public function test_grab()
    {
        $grabbed = Config::get('grabber');
        $grabbedCount = count($grabbed);
        $this->domParser
            ->shouldReceive('setCrawler')
            ->times($grabbedCount);

        $expected = [];
        foreach ($grabbed as $grab) {
            $rand = rand(10, 1000);
            $this->curler->shouldReceive('curl')
                ->once()
                ->with($grab['url'])
                ->andReturn('foo');

            $this->domParser
                ->shouldReceive('getSiblings')
                ->with($grab['element'])
                ->once()
                ->andReturn($rand);

            $expected[] = $rand;
        }

        $this->grabber = Mockery::mock(Grabber::class, [$this->curler, $this->domParser])->makePartial();
        $results = $this->grabber->grab();

        $this->assertEquals($expected, $results);
    }
}
