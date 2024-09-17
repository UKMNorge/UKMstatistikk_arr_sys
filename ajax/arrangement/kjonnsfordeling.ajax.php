<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\ArrSys\HandleAPICallWithAuthorization;
use UKMNorge\Statistikk\Objekter\StatistikkArrangement;

$plId = HandleAPICallWithAuthorization::getArgumentBeforeInit('plId', 'POST');

if($plId == null) {
    HandleAPICallWithAuthorization::sendError('Mangler arrangement ID', 400);
}

$tilgang = 'arrangement_i_kommune_fylke'; // Er admin i arrangement eller er admin i kommune eller fylke som arrangementet tilhører
$tilgangAttribute = $plId; // Sender riktig arrangement id til tilgangskontroll

$handleCall = new HandleAPICallWithAuthorization(['plId'], [], ['GET', 'POST'], false, false, $tilgang, $tilgangAttribute);

$plId = $handleCall->getArgument('plId');

$arrangement = null;
try{
    $arrangement = new Arrangement($plId);
} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
}

$statArr = new StatistikkArrangement($arrangement->getId(), $arrangement->getSesong());


$handleCall->sendToClient($statArr->getKjonnsfordeling());