<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Database\SQL\Query;
use UKMNorge\Statistikk\Objekter\StatistikkArrangement;



// Det brukes POST fordi WP tillater POST bare
$handleCall = new HandleAPICall(['plId', 'unike'], [], ['GET', 'POST'], false);
$plId = $handleCall->getArgument('plId');
$erUnike = $handleCall->getArgument('unike');

$arrangement = null;
try{
    $arrangement = new Arrangement(3620);
} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
}

$statArr = new StatistikkArrangement($arrangement);

$retArr = [];
$retArr['erUnike'] = $erUnike == 'true';

if($erUnike == 'true') {
    $retArr['antall'] = $statArr->getAntallUnikeDeltakere();
}
else {
    $retArr['antall'] = $statArr->getAntallDeltakere();
}

$handleCall->sendToClient($retArr);