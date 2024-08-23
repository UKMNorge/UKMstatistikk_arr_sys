<?php

use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Database\SQL\Query;
use UKMNorge\Geografi\Fylke;
use UKMNorge\Geografi\Fylker;
use UKMNorge\Statistikk\Objekter\StatistikkFylke;



// Det brukes POST fordi WP tillater POST bare
$handleCall = new HandleAPICall(['fylkeId', 'season', 'unike'], [], ['GET', 'POST'], false);
$fylkeId = $handleCall->getArgument('fylkeId');
$season = $handleCall->getArgument('season');
$erUnike = $handleCall->getArgument('unike') == 'true';

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
$retArr['erUnike'] = $erUnike;

if($erUnike) {
    $retArr['antall'] = $statFylke->getAntallUnikeDeltakere();
}
else {
    $retArr['antall'] = $statFylke->getAntallDeltakere();
}

$handleCall->sendToClient($retArr);