<?php

// Sjangerfordeling på fylke

use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Database\SQL\Query;
use UKMNorge\Geografi\Fylke;
use UKMNorge\Geografi\Fylker;
use UKMNorge\Statistikk\Objekter\StatistikkFylke;


// Det brukes POST fordi WP tillater POST bare
$handleCall = new HandleAPICall(['fylkeId', 'season'], ['excludePlId'], ['GET', 'POST'], false);
$excludePlId = $handleCall->getOptionalArgument('excludePlId');
$fylkeId = $handleCall->getArgument('fylkeId');
$season = $handleCall->getArgument('season');

$fylke = Fylker::getById($fylkeId);

$statFylke = new StatistikkFylke($fylke, $season);


$handleCall->sendToClient($statFylke->getSjangerFordeling($excludePlId ?? -1));