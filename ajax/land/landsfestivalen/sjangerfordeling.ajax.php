<?php

use UKMNorge\Arrangement\UKMFestival;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\OAuth2\ArrSys\HandleAPICallWithAuthorization;
use UKMNorge\Statistikk\Objekter\StatistikkArrangement;


$season = HandleAPICallWithAuthorization::getArgumentBeforeInit('season', 'POST');

if($season == null) {
    HandleAPICallWithAuthorization::sendError('Mangler sesong', 400);
}

$arrangement = null;
try{
    $arrangement = UKMFestival::getBySeason($season);
} catch(Exception $e) {
    if($e->getCode() == 401) {
        HandleAPICallWithAuthorization::sendError($e->getMessage(), 401);
    }
    HandleAPICallWithAuthorization::sendError('Kunne ikke hente arrangementet', 401);
}

// Etter at arrangementet er hentet, sjekk om brukeren har tilgang til å se statistikk for arrangementet
$tilgang = 'arrangement';
$tilgangAttribute = $arrangement->getId();

$handleCall = new HandleAPICallWithAuthorization(['season', 'unike'], [], ['GET', 'POST'], false, false, $tilgang, $tilgangAttribute);


$statArr = new StatistikkArrangement($arrangement->getId(), $arrangement->getSesong());

$handleCall->sendToClient($statArr->getSjangerfordeling());