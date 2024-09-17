<?php

// Sjangerfordeling på fylke

use UKMNorge\OAuth2\ArrSys\HandleAPICallWithAuthorization;
use UKMNorge\Database\SQL\Query;
use UKMNorge\Geografi\Fylke;
use UKMNorge\Geografi\Fylker;
use UKMNorge\Statistikk\Objekter\StatistikkFylke;

$fylkeId = HandleAPICallWithAuthorization::getArgumentBeforeInit('fylkeId', 'POST');

if($fylkeId == null) {
    HandleAPICallWithAuthorization::sendError('Mangler fylkeId', 400);
}

$tilgang = 'fylke_fra_kommune';
$tilgangAttribute = $fylkeId; // Er admin i kommune som tilhører fylke med id $fylkeId

$handleCall = new HandleAPICallWithAuthorization(['fylkeId', 'season'], ['excludePlId'], ['GET', 'POST'], false, false, $tilgang, $tilgangAttribute);

$excludePlId = $handleCall->getOptionalArgument('excludePlId');
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

$handleCall->sendToClient($statFylke->getSjangerFordeling($excludePlId ?? -1));