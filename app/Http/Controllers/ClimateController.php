<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

class ClimateController extends Controller
{
    private $config = [
        'consumer_key'    => 'dj0yJmk9elZCMzhoSEZ0eGJNJmQ9WVdrOVVXRldUVXhRYUcwbWNHbzlNQT09JnM9Y29uc3VtZXJzZWNyZXQmc3Y9MCZ4PTcx',
        'consumer_secret' => 'a647f23c320cebbb912711394593682ad4b856e6',
        'signature_method' => Oauth1::SIGNATURE_METHOD_HMAC,
        'token'           => '',
        'token_secret'    => '',
        'App-Id' => 'QaVMLPhm'
    ];

    public function index(){

        $stack = HandlerStack::create();

        $middleware = new Oauth1($this->config);
        $stack->push($middleware);

        $client = new Client([
            'base_uri' => 'https://weather-ydn-yql.media.yahoo.com/',
            'handler' => $stack,
            'auth' => 'oauth'
        ]);

        $data = [
            'headers' => [
                'X-Yahoo-App-Id' => $this->config['App-Id'],
            ],
            'query' =>[
                'location' => 'Miami',
                'u' => 'c',
                'format' => 'json'
            ]
        ];

      
        $response = $client->get('forecastrss',$data);
  
        $body = $response->getBody()->getContents();
        print_r($body);
        exit;
    }
}
