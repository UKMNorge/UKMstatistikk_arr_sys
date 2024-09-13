<?php

use UKMNorge\Geografi\Fylke;
use UKMNorge\Statistikk\StatistikkHandleAPICall;
use UKMNorge\Statistikk\Objekter\StatistikkFylke;
use UKMNorge\Statistikk\Objekter\StatistikkArrangement;


$tilgang = 'fylke'; // Kreves tilgang som superadmin for å se statistikk for alle kommuner
$tilgangAttribute = null; // Er admin i minst 1 fylke

$handleCall = new StatistikkHandleAPICall(['season'], ['excludePlId'], ['GET', 'POST'], false, false, $tilgang, $tilgangAttribute);

$season = $handleCall->getArgument('season');
$excludePlId = $handleCall->getOptionalArgument('excludePlId') ?? -1;

$alleFylkerISesong = StatistikkFylke::getAlleFylkeIdFraSSB($season);
$antall = 0;
$antallArrangementer = 0;

foreach($alleFylkerISesong as $fylkeId => $fylkeNavn) {
    $fylke = new Fylke($fylkeId, '', (string)$fylkeNavn, true);
    $statistikkFylke = new StatistikkFylke($fylke, $season);
    
    $arrangementerIds = $statistikkFylke->getFylkesarrangementerIds();

    foreach($arrangementerIds as $arrangementId) {
        if($arrangementId == $excludePlId) {
            continue;
        }
        $statArr = new StatistikkArrangement((int)$arrangementId, $season);

        $antall += $statArr->getAntallDeltakere();
        $antallArrangementer++;
    }
}

$handleCall->sendToClient(['antall' => intval($antall/$antallArrangementer)]);