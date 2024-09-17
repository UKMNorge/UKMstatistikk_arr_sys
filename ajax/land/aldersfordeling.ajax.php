<?php

use UKMNorge\Geografi\Fylke;
use UKMNorge\Statistikk\Objekter\StatistikkFylke;
use UKMNorge\OAuth2\ArrSys\HandleAPICallWithAuthorization;


$tilgang = 'fylke';
$tilgangAttribute = null; // Kreves kun tilgang til fylkenivå, ikke spesifikt fylke

$handleCall = new HandleAPICallWithAuthorization(['season'], [], ['GET', 'POST'], false, false, $tilgang, $tilgangAttribute);
$season = $handleCall->getArgument('season');

$alleFylkerISesong = StatistikkFylke::getAlleFylkeIdFraSSB($season);

$retArr = [];
foreach($alleFylkerISesong as $fylkeId => $fylkeNavn) {
    $fylke = new Fylke($fylkeId, '', (string)$fylkeNavn, true);
    $statFylke = new StatistikkFylke($fylke, $season);
    foreach($statFylke->getAldersfordeling() as $af) {
        if(!isset($retArr[$af['age']])) {
            $retArr[$af['age']] = 0;
        }
        $retArr[$af['age']] += $af['antall'];
    }
}

$handleCall->sendToClient($retArr);
