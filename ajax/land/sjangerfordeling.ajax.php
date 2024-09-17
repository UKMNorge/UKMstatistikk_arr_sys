<?php

use UKMNorge\Geografi\Fylke;
use UKMNorge\OAuth2\ArrSys\HandleAPICallWithAuthorization;
use UKMNorge\Statistikk\Objekter\StatistikkFylke;

$tilgang = 'fylke';
$tilgangAttribute = null; // Er admin i minst 1 fylke

$handleCall = new HandleAPICallWithAuthorization(['season'], [], ['GET', 'POST'], false, false, $tilgang, $tilgangAttribute);

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
