<?php

namespace APITestBundle\Entity;

class Meteo{

    public function traitement(int $nbJours, string $lieu, string $lang){
        if ($nbJours > 10 || $nbJours <= 0){
            return $response="Erreur:le nombre de jours doit etre en 1 et 10!";
        }
        else {
            $json_string = file_get_contents("http://api.wunderground.com/api/33e139d5020218bb/forecast10day/lang:$lang/q/IA/pws:$lieu.json");
            $parsed_json = json_decode($json_string);
            $response = "<h1>Météo des 5 prochains jours sur la station $lieu (Version code moins moche...):</h1>
                        <table>
                            <tr>
                                <td>Jours</td>
                                <td>Température Max en °C</td>
                                <td>Température Min en °C</td>
                            </tr>";
            for ($i = 0; $i < $nbJours; $i++) {
                $location = $parsed_json->{'forecast'}->{'simpleforecast'}->{'forecastday'}[$i];
                $high = $location->{'high'}->{'celsius'};
                $low = $location->{'low'}->{'celsius'};
                $date = $location->{'date'}->{'day'} . " " . $location->{'date'}->{'monthname'} . " " . $location->{'date'}->{'year'};
                $response .= "<tr>
                                <td>$date</td>
                                <td>$high</td>
                                <td>$low</td>
                            </tr>";
            }
            return $response .= "</table>";
        }
    }

    public function traitementRender(string $lieu, string $lang){
        $json_string = file_get_contents("http://api.wunderground.com/api/33e139d5020218bb/forecast10day/lang:$lang/q/IA/pws:$lieu.json");
        $parsed_json = json_decode($json_string);
        return (!isset($parsed_json->{'forecast'})? "" : $parsed_json->{'forecast'}->{'simpleforecast'}->{'forecastday'});
    }
}