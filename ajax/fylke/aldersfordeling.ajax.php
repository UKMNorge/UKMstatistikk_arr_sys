<?php

use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Database\SQL\Query;
use UKMNorge\Geografi\Fylke;
use UKMNorge\Geografi\Fylker;
use UKMNorge\Statistikk\Objekter\StatistikkFylke;



// Det brukes POST fordi WP tillater POST bare
$handleCall = new HandleAPICall(['fylkeId', 'season'], [], ['GET', 'POST'], false);
$fylkeId = $handleCall->getArgument('fylkeId');
$season = $handleCall->getArgument('season');

$fylke = null;
try{
    $fylke = Fylker::getById($fylkeId);
} catch(Exception $e) {
    $handleCall->sendErrorToClient('Kunne ikke hente fylke', 401);
}

$statFylke = new StatistikkFylke($fylke, $season);

$handleCall->sendToClient($statFylke->getAldersfordeling());