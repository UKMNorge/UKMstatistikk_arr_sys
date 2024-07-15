<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Database\SQL\Query;
use UKMNorge\Statistikk\Objekter\StatistikkArrangement;



// Det brukes POST fordi WP tillater POST bare
$handleCall = new HandleAPICall(['plId'], [], ['GET', 'POST'], false);
$plId = $handleCall->getArgument('plId');

$arrangement = null;
try{
    $arrangement = new Arrangement(3620);
} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente arrangementet', 401);
}


$statArr = new StatistikkArrangement($arrangement);


var_dump('UNIKE antallDeltakere: ' . $statArr->getAntallUnikeDeltakere());
var_dump('IKKE UNIKE antallDeltakere: ' . $statArr->getAntallDeltakere());



// $allePlIds = getAllArrangements();
// $sesong = 2024;
// $count = 0;
// // foreach($allePlIds as $plId) {
//     // $arrangement = new Arrangement($plId);
//     $statArr = new StatistikkArrangement($arrangement);

//     $antallDeltakere = $statArr->getAntallUnikeDeltakere();

//     var_dump('UNIKE antallDeltakere: ' . $statArr->getAntallUnikeDeltakere());
//     var_dump('IKKE UNIKE antallDeltakere: ' . $statArr->getAntallIkkeUnikeDeltakere());

// // }

// var_dump('ANTALL: ' . $count);

// die;


// $statsObj = $arrangement->getStatistikk();

// // $statsObj->setKommune([$arrangement->getKommune()->getId()]);


// // Returner til klienten
// $handleCall->sendToClient($statsObj->getStatArrayPerson(2024));


// function getAntallPersoner(Arrangement $arrangement) : int {
//     $antallPersoner = 0;
//     foreach($arrangement->getInnslag()->getAll() as $innslag) {
//         $antallPersoner += $innslag->getPersoner()->getAntall();
//     }
//     return $antallPersoner;
// }

// function getAllArrangements() : array {
//     $alleArrangementer = [];

//     $sql = new Query(
//         "SELECT pl_id FROM `smartukm_place` WHERE season > 2015",
//     );

//     $res = $sql->run();
//     if (Query::numRows($res) > 0) {
//         while ($r = Query::fetch($res)) {
//             $alleArrangementer[] = $r['pl_id'];
//         }
//     }

//     return $alleArrangementer;
// }