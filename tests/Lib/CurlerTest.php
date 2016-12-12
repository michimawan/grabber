<?php

use Curl\Curl;
use App\Lib\Curler;

class CurlerTest extends BaseLibTest
{
    public function test_curl_when_get_http_status_code_OK()
    {
        $url = 'foo';
        Config::set('grabber.url', $url);
        $curl = Mockery::mock(Curl::class)->makePartial();
        $curl->shouldReceive('get')->with($url)->once();
        $curl->http_status_code = 201;
        $curl->response = 'haha';

        $curler = new Curler();
        $curler->setCurl($curl);
        $response = $curler->curl();
        $this->assertEquals('haha', $response);
    }

    public function test_curl_when_get_http_status_code_not_200()
    {
        $url = 'www.google.com';
        Config::set('grabber.url', $url);
        $curl = Mockery::mock(Curl::class)->makePartial();
        $curl->shouldReceive('get')->with($url)->once();
        $curl->http_status_code = 301;
        $curl->response = 'redirected';

        $curler = new Curler();
        $curler->setCurl($curl);
        $response = $curler->curl();
        $this->assertEquals('', $response);
    }
}
