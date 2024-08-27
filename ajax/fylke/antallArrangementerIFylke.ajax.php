<?php

use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Geografi\Fylker;
use UKMNorge\Statistikk\Objekter\StatistikkFylke;


$handleCall = new HandleAPICall(['fylkeId', 'season'], [], ['GET', 'POST'], false);
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



