<?php

use UKMNorge\Arrangement\UKMFestival;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Statistikk\StatistikkHandleAPICall;
use UKMNorge\Statistikk\Objekter\StatistikkArrangement;


$season = StatistikkHandleAPICall::getArgumentBeforeInit('season', 'POST');

if($season == null) {
    $handleCall->sendErrorToClient('Mangler sesong', 400);
}


$arrangement = null;
try{
    $arrangement = UKMFestival::getBySeason($season);
} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
}

// Etter at arrangementet er hentet, sjekk om brukeren har tilgang til å se statistikk for arrangementet
$tilgang = 'arrangement';
$tilgangAttribute = $arrangement->getId();

// The instance is created only if the user has access to the statistics
$handleCall = new StatistikkHandleAPICall(['season', 'unike'], [], ['GET', 'POST'], false, false, $tilgang, $tilgangAttribute);

$statArr = new StatistikkArrangement($arrangement->getId(), $arrangement->getSesong());

$handleCall->sendToClient($statArr->getAldersfordeling());