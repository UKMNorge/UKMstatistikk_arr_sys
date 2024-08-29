<?php

use UKMNorge\Geografi\Fylke;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Statistikk\Objekter\StatistikkFylke;


$handleCall = new HandleAPICall(['season'], [], ['GET', 'POST'], false);
$season = $handleCall->getArgument('season');

$alleFylkerISesong = StatistikkFylke::getAlleFylkeIdFraSSB($season);

$retArr = [];

foreach($alleFylkerISesong as $fylkeId => $fylkeNavn) {
    $fylke = new Fylke($fylkeId, '', (string)$fylkeNavn, true);
    $statFylke = new StatistikkFylke($fylke, $season);
    
    $retArr[$fylkeId] = ['fylke_navn' => $fylkeNavn, 'antall_innslag' =>$statFylke->getAntallInnslag()];
}

$handleCall->sendToClient($retArr);
