<?php

// Antall kommuner i fylket som har UKM-aktivtet (relatert til antall kommuner totalt)

use UKMNorge\OAuth2\ArrSys\HandleAPICallWithAuthorization;
use UKMNorge\Geografi\Fylker;
use UKMNorge\Statistikk\Objekter\StatistikkFylke;

$fylkeId = HandleAPICallWithAuthorization::getArgumentBeforeInit('fylkeId', 'POST');

if($fylkeId == null) {
    HandleAPICallWithAuthorization::sendError('Mangler fylkeId', 400);
}

$tilgang = 'fylke_fra_kommune';
$tilgangAttribute = $fylkeId; // Er admin i kommune som tilhører fylke med id $fylkeId

$handleCall = new HandleAPICallWithAuthorization(['fylkeId', 'season'], [], ['GET', 'POST'], false, false, $tilgang, $tilgangAttribute);

$fylkeId = $handleCall->getArgument('fylkeId');
$season = $handleCall->getArgument('season');


$fylke = null;
try{
    $fylke = Fylker::getById($fylkeId);
} catch(Exception $e) {
    $handleCall->sendErrorToClient('Kunne ikke hente fylke', 500);
}

$statFylke = null;
try{
    $statFylke = new StatistikkFylke($fylke, $season);
} catch(Exception $e) {
    $handleCall->sendErrorToClient('Kunne ikke hente statistikk for fylke', 401);
}

$handleCall->sendToClient($statFylke->getKommunerAktivitet());