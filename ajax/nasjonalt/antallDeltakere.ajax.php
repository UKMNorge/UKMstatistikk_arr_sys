<?php

use UKMNorge\DesignWordpress\Environment\Statistikk;
use UKMNorge\Geografi\Fylke;
use UKMNorge\OAuth2\ArrSys\HandleAPICallWithAuthorization;
use UKMNorge\Statistikk\Objekter\StatistikkNasjonalt;


$tilgang = 'superadmin'; // Kreves tilgang som superadmin for å se statistikken
$tilgangAttribute = null;

$handleCall = new HandleAPICallWithAuthorization(['season', 'unike'], [], ['GET', 'POST'], false, false, $tilgang, $tilgangAttribute);

$season = $handleCall->getArgument('season');
$erUnike = $handleCall->getArgument('unike') == 'true';

$statNasjonalt = new StatistikkNasjonalt($season);

$antall = $erUnike ? $statNasjonalt->getAntallUnikeDeltakere() : $statNasjonalt->getAntallDeltakere();

$handleCall->sendToClient([
    'unike' => $erUnike, 
    'antallDeltakere' => $antall,
    'antallDeltakereUfullforte' => $statNasjonalt->getAntallUnikeUfullforteDeltakere(),
]);
