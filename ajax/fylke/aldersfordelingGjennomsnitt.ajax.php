<?php

use UKMNorge\OAuth2\ArrSys\HandleAPICallWithAuthorization;
use UKMNorge\Geografi\Fylke;
use UKMNorge\Geografi\Fylker;
use UKMNorge\Statistikk\Objekter\StatistikkFylke;
use UKMNorge\Database\SQL\Insert;


$tilgang = 'fylke'; // Er admin i minst 1 fylke
$tilgangAttribute = null;

$handleCall = new HandleAPICallWithAuthorization(['fra', 'til'], [], ['GET', 'POST'], false, false, $tilgang, $tilgangAttribute);
$fra = $handleCall->getArgument('fra');
$til = $handleCall->getArgument('til');

$retArr = StatistikkFylke::getGjennomsnittAldersfordelingIAlleFylker($fra, $til);

$handleCall->sendToClient($retArr);