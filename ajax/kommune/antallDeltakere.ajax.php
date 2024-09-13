<?php

use UKMNorge\Statistikk\StatistikkHandleAPICall;
use UKMNorge\Geografi\Kommune;
use UKMNorge\Statistikk\Objekter\StatistikkKommune;

$kommuneId = StatistikkHandleAPICall::getArgumentBeforeInit('kommuneId', 'POST');

if($kommuneId == null) {
    StatistikkHandleAPICall::sendError('Mangler kommuneId', 400);
}

$tilgang = 'kommune'; // Er admin i kommune
$tilgangAttribute = $kommuneId; // Er admin i kommune med id $kommuneId

$handleCall = new StatistikkHandleAPICall(['kommuneId', 'season', 'unike'], [], ['GET', 'POST'], false, false, $tilgang, $tilgangAttribute);

$kommuneId = $handleCall->getArgument('kommuneId');
$season = $handleCall->getArgument('season');
$erUnike = $handleCall->getOptionalArgument('unike') == 'true';

$kommune = null;
try{
    $kommune = new Kommune($kommuneId);
} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 500);
}


$statKom = null;
try{
    $statKom = new StatistikkKommune($kommune, $season);
} catch(Exception $e) {
    $handleCall->sendErrorToClient('Kunne ikke hente statistikk for kommune', 401);
}

$retArr = [];
$retArr['erUnike'] = $erUnike;

if($erUnike) {
    $retArr['antall'] = $statKom->getAntallUnikeDeltakere();
}
else {
    $retArr['antall'] = $statKom->getAntallDeltakere();
}

$handleCall->sendToClient($retArr);