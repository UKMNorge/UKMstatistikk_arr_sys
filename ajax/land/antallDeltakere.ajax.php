<?php

use UKMNorge\DesignWordpress\Environment\Statistikk;
use UKMNorge\Geografi\Fylke;
use UKMNorge\OAuth2\ArrSys\HandleAPICallWithAuthorization;
use UKMNorge\Statistikk\Objekter\StatistikkFylke;


$tilgang = 'fylke';
$tilgangAttribute = null; // Er admin i minst 1 fylke

$handleCall = new HandleAPICallWithAuthorization(['season', 'unike'], [], ['GET', 'POST'], false, false, $tilgang, $tilgangAttribute);

$season = $handleCall->getArgument('season');
$erUnike = $handleCall->getArgument('unike') == 'true';

$alleFylkerISesong = StatistikkFylke::getAlleFylkeIdFraSSB($season);

$antall = 0;
foreach($alleFylkerISesong as $fylkeId => $fylkeNavn) {
    $fylke = new Fylke($fylkeId, '', (string)$fylkeNavn, true);
    $statistikkFylke = new StatistikkFylke($fylke, $season);
    
    var_dump($fylke->getNavn() . ' = ' . $statistikkFylke->getAntallUnikeDeltakere() .' |');

    if($erUnike) {
        $antallDeltakere = $statistikkFylke->getAntallUnikeDeltakere();
    } else {
        $antallDeltakere = $statistikkFylke->getAntallDeltakere();
    }
    $antall += $antallDeltakere;
}

$handleCall->sendToClient(['antall' => $antall]);
