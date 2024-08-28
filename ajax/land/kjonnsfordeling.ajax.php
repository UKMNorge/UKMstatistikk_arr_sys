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
    
    // var_dump($statFylke->getKjonnsfordeling());
    foreach($statFylke->getKjonnsfordeling() as $key => $antall) {
        if(!isset($retArr[$key])) {
            $retArr[$key] = 0;
        }
        $retArr[$key] += $antall;
    }
}

$handleCall->sendToClient($retArr);
