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

$handleCall = new HandleAPICallWithAuthorization(['plId', 'unike'], [], ['GET', 'POST'], false, false, $tilgang, $tilgangAttribute);

$plId = $handleCall->getArgument('plId');
$erUnike = $handleCall->getArgument('unike');

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

$retArr = [];
$retArr['erUnike'] = $erUnike == 'true';
$retArr['season'] = $arrangement->getSesong();

if($erUnike == 'true') {
    $antallUnikeAlle = intval($statArr->getAntallUnikeDeltakere());
    $antallUnikeUfullforte = intval($statArr->getAntallUnikeDeltakere(true));

    $retArr['antall'] = $antallUnikeAlle - $antallUnikeUfullforte;
    $retArr['antallUfullforte'] = $antallUnikeUfullforte;
}
else {
    $antallDeltakereAlle = intval($statArr->getAntallDeltakere());
    $antallDeltakereUfullforte = intval($statArr->getAntallDeltakere(true));

    $retArr['antall'] = $antallDeltakereAlle - $antallDeltakereUfullforte;
    $retArr['antallUfullforte'] = $antallDeltakereUfullforte;
}

$handleCall->sendToClient($retArr);