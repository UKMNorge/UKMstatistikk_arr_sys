<?php

use UKMNorge\Arrangement\UKMFestival;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Statistikk\Objekter\StatistikkArrangement;



// Det brukes POST fordi WP tillater POST bare
$handleCall = new HandleAPICall(['season'], [], ['POST'], false);
$season = $handleCall->getArgument('season');

$arrangement = null;
try{
    $arrangement = UKMFestival::getBySeason($season);
} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
}


$statArr = new StatistikkArrangement($arrangement->getId(), $arrangement->getSesong());


$handleCall->sendToClient($statArr->getSjangerfordeling());