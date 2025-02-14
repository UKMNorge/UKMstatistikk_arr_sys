<?php

use UKMNorge\OAuth2\ArrSys\HandleAPICallWithAuthorization;
use UKMNorge\Statistikk\Objekter\StatistikkNasjonalt;

$tilgang = 'superadmin'; // Kreves tilgang som superadmin for å se statistikken
$tilgangAttribute = null;

$handleCall = new HandleAPICallWithAuthorization(['season'], [], ['GET', 'POST'], false, false, $tilgang, $tilgangAttribute);
$season = $handleCall->getArgument('season');

$retArr = [];

    $statFylke = new StatistikkNasjonalt($season);
    foreach($statFylke->getAldersfordeling() as $af) {
        if(!isset($retArr[$af['age']])) {
            $retArr[$af['age']] = 0;
        }
        $retArr[$af['age']] += $af['antall'];
    }

$handleCall->sendToClient($retArr);
