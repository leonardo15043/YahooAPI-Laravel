<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Mapper;
use App\Climate;

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

    public function getLocation($location){

        $stack = HandlerStack::create();
        $middleware = new Oauth1($this->config);
        $stack->push($middleware);
        

        $client = new Client([
            'base_uri' => 'https://weather-ydn-yql.media.yahoo.com/',
            'handler' => $stack,
            'auth' => 'oauth'
        ]);

        $headers = [
            'headers' => [
                'X-Yahoo-App-Id' => $this->config['App-Id'],
            ]     
        ];

        $query = [
            'query' =>[
                'location' => $location,
                'u' => 'c',
                'format' => 'json'
            ]
        ];

        
        $response = $client->get('forecastrss',$query,$headers);
        $body = $response->getBody()->getContents();
        $body = json_decode($body, true);

        $this->saveReport($body['location']['city'],$body['current_observation']['atmosphere']['humidity']);
        
        return $body;

    }

    public function index(){

        $dataMiami = $this->getLocation('Miami');
       
        Mapper::map($dataMiami['location']['lat'], $dataMiami['location']['long'], [
            'zoom' => 5,
            'center' => true, 
            'marker' => false, 
            'markers' => [
                'title' => $dataMiami['location']['city'], 
                'icon' => 'img/humedad.png' 
            ],
        ]);

        Mapper::informationWindow(
            $dataMiami['location']['lat'], $dataMiami['location']['long'], 
            "<b>".$dataMiami['location']['city']."</b><br><br><b>Humedad: </b> ".$dataMiami['current_observation']['atmosphere']['humidity'],
            [
                'marker' => true,
                    'title' => $dataMiami['location']['city'], 
                    'icon' => 'assets/img/humedad.png' 
            ]
        );


        $dataOrlando = $this->getLocation('Orlando');

        Mapper::informationWindow(
            $dataOrlando['location']['lat'], $dataOrlando['location']['long'], 
            "<b>".$dataOrlando['location']['city']."</b><br><br><b>Humedad: </b> ".$dataOrlando['current_observation']['atmosphere']['humidity'],
            [
                'marker' => true,
                    'title' => $dataOrlando['location']['city'], 
                    'icon' => 'assets/img/humedad.png' 
            ]
        );

        $dataNewYork = $this->getLocation('New York');

        Mapper::informationWindow(
            $dataNewYork['location']['lat'], $dataNewYork['location']['long'], 
            "<b>".$dataNewYork['location']['city']."</b><br><br><b>Humedad: </b> ".$dataNewYork['current_observation']['atmosphere']['humidity'],
            [
                'marker' => true,
                    'title' => $dataNewYork['location']['city'], 
                    'icon' => 'assets/img/humedad.png' 
            ]
        );

        $history = Climate::all();

        return view('climate',compact('history'));
      
    }

    public function saveReport($city,$humidity)
    {
        $coincidence = Climate::where('humidity', $humidity)->where('city', $city)->whereDay('created_at', '=', date('d'))->whereMonth('created_at', '=', date('m'))->whereYear('created_at', '=', date('Y'))->count();
 
        if($coincidence == 0){
            $climate = new Climate([
                'city' => $city,
                'humidity' => $humidity
            ]);
            $climate->save();
        }
       
    }
}
