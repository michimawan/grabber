<?php
namespace App\Lib;

use Curl\Curl;
use Config;

class Curler
{
    private $curl;

    public function getCurl()
    {
        return $this->curl;
    }

    public function setCurl(Curl $curl)
    {
        $this->curl = $curl;
    }

    public function curl($url)
    {
        $this->curl->get($url);

        if ($this->curl->http_status_code >= 200 &&
            $this->curl->http_status_code < 300) {
            $response = $this->curl->response;
        } else {
            $response = '';
        }

        return $response;
    }
}
