<?php

use UKMNorge\Geografi\Fylke;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Statistikk\Objekter\StatistikkFylke;


$handleCall = new HandleAPICall(['season'], [], ['GET', 'POST'], false);
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
