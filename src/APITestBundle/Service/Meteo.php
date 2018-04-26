<?php

namespace APITestBundle\Service;

class Meteo
{
    private $urlAPI = "http://api.wunderground.com/api/33e139d5020218bb";
    public function traitementRender(string $lieu, string $lang){
        $json_string = file_get_contents( $this->urlAPI."/forecast10day/lang:$lang/q/pws:$lieu.json");
        $parsed_json = json_decode($json_string);
        return (!isset($parsed_json->{'forecast'})? "" : $parsed_json->{'forecast'}->{'simpleforecast'}->{'forecastday'});
    }
}