<?php

use UKMNorge\Statistikk\StatistikkHandleAPICall;
use UKMNorge\Database\SQL\Query;
use UKMNorge\Geografi\Fylke;
use UKMNorge\Geografi\Fylker;
use UKMNorge\Statistikk\Objekter\StatistikkFylke;


$fylkeId = StatistikkHandleAPICall::getArgumentBeforeInit('fylkeId', 'POST');

if($fylkeId == null) {
    StatistikkHandleAPICall::sendError('Mangler fylkeId', 400);
}

$tilgang = 'fylke_fra_kommune'; // Er admin i kommune
$tilgangAttribute = $fylkeId; // Er admin i kommune med id $fylkeId

$handleCall = new StatistikkHandleAPICall(['fylkeId', 'season'], [], ['GET', 'POST'], false, false, $tilgang, $tilgangAttribute);

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
}
catch(Exception $e) {
    $handleCall->sendErrorToClient('Kunne ikke hente statistikk for fylke', 401);
}

$handleCall->sendToClient($statFylke->getAldersfordeling());