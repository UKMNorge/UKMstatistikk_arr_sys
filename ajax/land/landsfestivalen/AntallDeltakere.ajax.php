<?php

use UKMNorge\Arrangement\UKMFestival;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Statistikk\Objekter\StatistikkArrangement;
use UKMNorge\Statistikk\StatistikkHandleAPICall;


$season = StatistikkHandleAPICall::getArgumentBeforeInit('season', 'POST');

if($season == null) {
    StatistikkHandleAPICall::sendError('Mangler sesong', 400);
}

$arrangement = null;
try{
    $arrangement = UKMFestival::getBySeason($season);
} catch(Exception $e) {
    if($e->getCode() == 401) {
        StatistikkHandleAPICall::sendError($e->getMessage(), 401);
    }
    StatistikkHandleAPICall::sendError('Kunne ikke hente arrangementet', 401);
}

// Etter at arrangementet er hentet, sjekk om brukeren har tilgang til å se statistikk for arrangementet
$tilgang = 'arrangement';
$tilgangAttribute = $arrangement->getId();

$handleCall = new StatistikkHandleAPICall(['season', 'unike'], [], ['GET', 'POST'], false, false, $tilgang, $tilgangAttribute);
$erUnike = $handleCall->getArgument('unike');


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