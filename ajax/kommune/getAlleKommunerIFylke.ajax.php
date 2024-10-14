<?php

use UKMNorge\Geografi\Fylker;
use UKMNorge\OAuth2\HandleAPICall;



$handleCall = new HandleAPICall(['fylkeId'], [], ['GET', 'POST'], false);

$fylkeId = $handleCall->getArgument('fylkeId');
$alleKommunerIFylke = [];

try{
    $fylke = Fylker::getById($fylkeId);
    $kommuner = $fylke->getKommuner();
    foreach($kommuner as $kommune) {
        $alleKommunerIFylke[] = [
            'id' => $kommune->getId(),
            'navn' => $kommune->getNavn()
        ];
    }    
} catch(Exception $e) {
    $handleCall->sendErrorToClient('Kunne ikke hente fylke', 401);
}

$handleCall->sendToClient($alleKommunerIFylke);