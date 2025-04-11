<?php

use UKMNorge\OAuth2\ArrSys\HandleAPICallWithAuthorization;
use UKMNorge\Database\SQL\Query;
use UKMNorge\Geografi\Fylke;
use UKMNorge\Geografi\Fylker;
use UKMNorge\Statistikk\Objekter\StatistikkFylke;


$fylkeId = HandleAPICallWithAuthorization::getArgumentBeforeInit('fylkeId', 'POST');

if($fylkeId == null) {
    HandleAPICallWithAuthorization::sendError('Mangler fylkeId', 400);
}

$tilgang = 'fylke';
$tilgangAttribute = $fylkeId; // Er admin i kommune som tilhører fylke med id $fylkeId

$handleCall = new HandleAPICallWithAuthorization(['fylkeId', 'season', 'unike'], [], ['GET', 'POST'], false, false, $tilgang, $tilgangAttribute);

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
$retArr['fylkeNavn'] = $fylke->getNavn();
$retArr['antallUregistrerteDeltakere'] = $statFylke->getAntallUregistrerteDeltakere();
$retArr['antallUregistrerteDeltakereFylke'] = $statFylke->getAntallUregistrerteDeltakereFylke();

if($erUnike) {
    $retArr['antall'] = $statFylke->getAntallUnikeDeltakereFylke();
}
else {
    $retArr['antall'] = $statFylke->getAntallDeltakereFylke();
}

$handleCall->sendToClient($retArr);