<?php

use UKMNorge\Statistikk\StatistikkHandleAPICall;
use UKMNorge\Statistikk\Objekter\StatistikkFylke;


$tilgang = 'fylke'; // Er admin i minst 1 fylke
$tilgangAttribute = null;

$handleCall = new StatistikkHandleAPICall(['season'], [], ['GET', 'POST'], false, false, $tilgang, $tilgangAttribute);
$season = $handleCall->getArgument('season');

$alleFylkerISesong = StatistikkFylke::getAlleFylkeIdFraSSB($season);

$retArr = [];
foreach($alleFylkerISesong as $fylkeId => $fylkeNavn) {
    $retArr[$fylkeId] = ['navn' => $fylkeNavn, 'antall' => StatistikkFylke::antallArrangementerIFylke($fylkeId, $season)];
}

$handleCall->sendToClient($retArr);



