<?php

use UKMNorge\Arrangement\UKMFestival;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Statistikk\Objekter\StatistikkArrangement;


$handleCall = new HandleAPICall(['season', 'unike'], [], ['GET', 'POST'], false);
$season = $handleCall->getArgument('season');
$erUnike = $handleCall->getArgument('unike');

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

$retArr = [];
$retArr['erUnike'] = $erUnike == 'true';

if($erUnike == 'true') {
    $retArr['antall'] = $statArr->getAntallUnikeDeltakere();
}
else {
    $retArr['antall'] = $statArr->getAntallDeltakere();
}

$handleCall->sendToClient($retArr);