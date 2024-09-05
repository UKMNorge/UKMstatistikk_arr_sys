<?php

use UKMNorge\DesignWordpress\Environment\Statistikk;
use UKMNorge\Geografi\Fylke;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Statistikk\Objekter\StatistikkFylke;
use UKMNorge\Statistikk\Objekter\StatistikkArrangement;
use UKMNorge\Database\SQL\Query;


$handleCall = new HandleAPICall(['season'], ['excludePlId'], ['GET', 'POST'], false);
$season = $handleCall->getArgument('season');
$excludePlId = $handleCall->getOptionalArgument('excludePlId') ?? -1;

$alleFylkerISesong = StatistikkFylke::getAlleFylkeIdFraSSB($season);
$antallArrangementer = 0;

$alleFylker = [];

foreach($alleFylkerISesong as $fylkeId => $fylkeNavn) {
    $fylke = new Fylke($fylkeId, '', (string)$fylkeNavn, true);
    $statistikkFylke = new StatistikkFylke($fylke, $season);
    
    $arrangementerIds = $statistikkFylke->getFylkesarrangementerIds();

    foreach($arrangementerIds as $arrangementId) {
        if($arrangementId == $excludePlId) {
            continue;
        }
        $statArr = new StatistikkArrangement((int)$arrangementId, $season);

        foreach($statArr->getSjangerFordeling() as $sjangers) {
            if(!isset($alleFylker[$sjangers['type_navn']])) {
                $alleFylker[$sjangers['type_navn']] = 0;
            }
            
            $alleFylker[$sjangers['type_navn']] += $sjangers['antall'];
        }

        $antallArrangementer++;
    }
}

foreach($alleFylker as $kjonn => $antall) {
    $alleFylker[$kjonn] = round($antall / $antallArrangementer, 2);
}

$handleCall->sendToClient($alleFylker);