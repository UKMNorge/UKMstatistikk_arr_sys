<?php

use UKMNorge\Geografi\Fylke;
use UKMNorge\Statistikk\StatistikkHandleAPICall;
use UKMNorge\Statistikk\Objekter\StatistikkFylke;

$tilgang = 'fylke';
$tilgangAttribute = null; // Er admin i minst 1 fylke

$handleCall = new StatistikkHandleAPICall(['season'], [], ['GET', 'POST'], false, false, $tilgang, $tilgangAttribute);

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
