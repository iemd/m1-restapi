<?php
/**
* Author: CodexWorld
* Author URI: http://www.codexworld.com
* Function Name: getLatLong()
* $address => Full address.
* Return => Latitude and longitude of the given address.
**/
function getLatLong($address){
    if(!empty($address)){
        //Formatted address
        $formattedAddr = str_replace(' ','+',$address);
        //Send request and receive json data by address
        $geocodeFromAddr = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.$formattedAddr.'&sensor=false'); 
        $output = json_decode($geocodeFromAddr);
        //Get latitude and longitute from json data
        $data['latitude']  = $output->results[0]->geometry->location->lat; 
        $data['longitude'] = $output->results[0]->geometry->location->lng;
        //Return latitude and longitude of the given address
        if(!empty($data)){
            return $data;
        }else{
            return false;
        }
    }else{
        return false;   
    }
}
/*
$address = 'Sipara, India, Patna, 800020, 2548645568';
$latLong = getLatLong($address);
echo $latitude = $latLong['latitude']?$latLong['latitude']:'Not found';
echo $longitude = $latLong['longitude']?$latLong['longitude']:'Not found';
*/
?>