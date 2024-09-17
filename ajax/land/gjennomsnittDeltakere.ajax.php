<?php

use UKMNorge\Geografi\Fylke;
use UKMNorge\OAuth2\ArrSys\HandleAPICallWithAuthorization;
use UKMNorge\Statistikk\Objekter\StatistikkFylke;


$tilgang = 'fylke'; 
$tilgangAttribute = null; // Er admin i minst 1 fylke

$handleCall = new HandleAPICallWithAuthorization(['season'], [], ['GET', 'POST'], false, false, $tilgang, $tilgangAttribute);

$season = $handleCall->getArgument('season');

$alleFylkerISesong = StatistikkFylke::getAlleFylkeIdFraSSB($season);

$retArr = [];
$gjennomsnitt = 0;
$antallFylker = 0;

foreach($alleFylkerISesong as $fylkeId => $fylkeNavn) {
    $fylke = new Fylke($fylkeId, '', (string)$fylkeNavn, true);
    $statFylke = new StatistikkFylke($fylke, $season);
    $gjennomsnitt += $statFylke->getGjennomsnittDeltakere($season);
    $antallFylker++;
}

$handleCall->sendToClient(['gjennomsnitt' => (int)intval($gjennomsnitt / $antallFylker)]);
