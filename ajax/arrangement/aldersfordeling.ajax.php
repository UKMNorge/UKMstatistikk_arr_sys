<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Database\SQL\Query;
use UKMNorge\Statistikk\Objekter\StatistikkArrangement;



// Det brukes POST fordi WP tillater POST bare
$handleCall = new HandleAPICall(['plId'], [], ['GET', 'POST'], false);
$plId = $handleCall->getArgument('plId');

$arrangement = null;
try{
    $arrangement = new Arrangement(40);
} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
}


$statArr = new StatistikkArrangement($arrangement);


echo 'Aldersofrdeling: ';
echo $statArr->getAldersfordeling();