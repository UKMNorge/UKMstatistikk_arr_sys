<?php

use UKMNorge\DesignWordpress\Environment\Statistikk;
use UKMNorge\Geografi\Fylke;
use UKMNorge\OAuth2\ArrSys\HandleAPICallWithAuthorization;
use UKMNorge\Statistikk\Objekter\StatistikkNasjonalt;

use Exeption;

$tilgang = 'superadmin'; // Kreves tilgang som superadmin for å se statistikken
$tilgangAttribute = null;

$handleCall = new HandleAPICallWithAuthorization(['season'], [], ['GET', 'POST'], false, false, $tilgang, $tilgangAttribute);

$season = $handleCall->getArgument('season');

$retArr = [];
try{
    $statNasjonalt = new StatistikkNasjonalt($season);
    
    $retArr = $statNasjonalt->getKjonnsfordeling();
} catch(Exception $e) {
    $handleCall->sendErrorToClient('Kunne ikke hente statistikk', $e->getMessage());
}

$handleCall->sendToClient($retArr);
