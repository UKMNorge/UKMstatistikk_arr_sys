<?php

use UKMNorge\DesignWordpress\Environment\Statistikk;
use UKMNorge\Geografi\Fylke;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Statistikk\Objekter\StatistikkFylke;


$handleCall = new HandleAPICall(['season', 'unike'], [], ['GET', 'POST'], false);
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
