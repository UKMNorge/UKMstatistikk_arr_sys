<?php

use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Statistikk\Objekter\StatistikkFylke;


$handleCall = new HandleAPICall(['season'], [], ['GET', 'POST'], false);
$season = $handleCall->getArgument('season');

$alleFylkerISesong = StatistikkFylke::getAlleFylkeIdFraSSB($season);

$retArr = [];
foreach($alleFylkerISesong as $fylkeId => $fylkeNavn) {
    $retArr[$fylkeId] = ['navn' => $fylkeNavn, 'antall' => StatistikkFylke::antallArrangementerIFylke($fylkeId, $season)];
}

$handleCall->sendToClient($retArr);



