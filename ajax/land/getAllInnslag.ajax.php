<?php

use UKMNorge\Geografi\Fylke;
use UKMNorge\Statistikk\StatistikkHandleAPICall;
use UKMNorge\Statistikk\Objekter\StatistikkFylke;

$tilgang = 'superadmin'; // Kreves tilgang som superadmin for å se statistikk for alle kommuner
$tilgangAttribute = null; 

$handleCall = new StatistikkHandleAPICall(['season'], [], ['GET', 'POST'], false, false, $tilgang, $tilgangAttribute);

$season = $handleCall->getArgument('season');

$alleFylkerISesong = StatistikkFylke::getAlleFylkeIdFraSSB($season);

$retArr = [];

foreach($alleFylkerISesong as $fylkeId => $fylkeNavn) {
    $fylke = new Fylke($fylkeId, '', (string)$fylkeNavn, true);
    $statFylke = new StatistikkFylke($fylke, $season);
    
    $retArr[$fylkeId] = ['fylke_navn' => $fylkeNavn, 'antall_innslag' =>$statFylke->getAntallInnslag()];
}

$handleCall->sendToClient($retArr);
