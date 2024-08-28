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
    
    foreach($statFylke->getSjangerFordeling() as $sjangers) {
       
        if(!isset($retArr[$sjangers['type_navn']])) {
            $retArr[$sjangers['type_navn']] = 0;
        }
        $retArr[$sjangers['type_navn']] += $sjangers['antall'];
    }
}

$handleCall->sendToClient($retArr);
