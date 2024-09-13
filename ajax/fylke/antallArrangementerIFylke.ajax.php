<?php

use UKMNorge\Statistikk\StatistikkHandleAPICall;
use UKMNorge\Geografi\Fylker;
use UKMNorge\Statistikk\Objekter\StatistikkFylke;


$tilgang = 'fylke'; // Er admin i minst 1 fylke
$tilgangAttribute = $fylkeId;

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
} catch(Exception $e) {
    $handleCall->sendErrorToClient('Kunne ikke hente statistikk for fylke', 401);
}

$retArr = [];
$retArr['antall'] = $statFylke->getAntallArrangementer();
$handleCall->sendToClient($retArr);