<?php

use UKMNorge\Arrangement\UKMFestival;
use UKMNorge\Statistikk\StatistikkHandleAPICall;
use UKMNorge\Statistikk\Objekter\StatistikkArrangement;


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


$statArr = new StatistikkArrangement($arrangement->getId(), $arrangement->getSesong());


$handleCall->sendToClient($statArr->getKjonnsfordeling());