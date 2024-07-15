<?php

// Kommune deltakere

use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Database\SQL\Query;
use UKMNorge\Geografi\Kommune;
use UKMNorge\Statistikk\Objekter\StatistikkKommune;



// Det brukes POST fordi WP tillater POST bare
$handleCall = new HandleAPICall(['plId'], [], ['GET', 'POST'], false);
$plId = $handleCall->getArgument('plId');

$kommune = null;
try{
    $kommune = new Kommune(5055);
} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
}


$season = 2020;


$statKom = new StatistikkKommune($kommune, $season);


var_dump('UNIKE antallDeltakere: ' . $statKom->getAntallUnikeDeltakere());
var_dump('IKKE UNIKE antallDeltakere: ' . $statKom->getAntallDeltakere());