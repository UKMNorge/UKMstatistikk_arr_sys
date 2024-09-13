<?php

use UKMNorge\DesignWordpress\Environment\Statistikk;
use UKMNorge\Geografi\Fylke;
use UKMNorge\Statistikk\StatistikkHandleAPICall;
use UKMNorge\Statistikk\Objekter\StatistikkFylke;


$tilgang = 'fylke';
$tilgangAttribute = null; // Er admin i minst 1 fylke

$handleCall = new StatistikkHandleAPICall(['season', 'unike'], [], ['GET', 'POST'], false, false, $tilgang, $tilgangAttribute);

$season = $handleCall->getArgument('season');
$erUnike = $handleCall->getArgument('unike') == 'true';

$alleFylkerISesong = StatistikkFylke::getAlleFylkeIdFraSSB($season);

$antall = 0;
foreach($alleFylkerISesong as $fylkeId => $fylkeNavn) {
    $fylke = new Fylke($fylkeId, '', (string)$fylkeNavn, true);
    $statistikkFylke = new StatistikkFylke($fylke, $season);

    if($erUnike) {
        $antallDeltakere = $statistikkFylke->getAntallUnikeDeltakere();
    } else {
        $antallDeltakere = $statistikkFylke->getAntallDeltakere();
    }
    $antall += $antallDeltakere;
}

$handleCall->sendToClient(['antall' => $antall]);
