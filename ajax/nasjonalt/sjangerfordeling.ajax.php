<?php

use UKMNorge\OAuth2\ArrSys\HandleAPICallWithAuthorization;
use UKMNorge\Statistikk\Objekter\StatistikkNasjonalt;

$tilgang = 'superadmin'; // Kreves tilgang som superadmin for å se statistikken
$tilgangAttribute = null;

$handleCall = new HandleAPICallWithAuthorization(['season'], [], ['GET', 'POST'], false, false, $tilgang, $tilgangAttribute);
$season = $handleCall->getArgument('season');

$retArr = [];

$statNasjonalt = new StatistikkNasjonalt($season);

foreach($statNasjonalt->getSjangerFordeling() as $sjangers) {
    
    if(!isset($retArr[$sjangers['type_navn']])) {
        $retArr[$sjangers['type_navn']] = 0;
    }
    $retArr[$sjangers['type_navn']] += $sjangers['antall'];
}

$handleCall->sendToClient($retArr);
