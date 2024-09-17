<?php

// Alle kommuner i hele landet som har UKM-aktivtet (relatert til antall kommuner totalt)

use UKMNorge\OAuth2\ArrSys\HandleAPICallWithAuthorization;
use UKMNorge\Statistikk\Objekter\StatistikkKommune;

$tilgang = 'superadmin'; // Kreves tilgang som superadmin for å se statistikk for alle kommuner
$tilgangAttribute = null; 

$handleCall = new HandleAPICallWithAuthorization(['season'], [], ['GET', 'POST'], false, false, $tilgang, $tilgangAttribute);
$season = $handleCall->getArgument('season');

$$kommunerAktivitet = null;
try{
    $kommunerAktivitet = StatistikkKommune::getAlleKommunerAktivitet($season);
} catch(Exception $e) {
    $handleCall->sendErrorToClient('Kunne ikke hente statistikk for kommune', 401);
}

$handleCall->sendToClient($kommunerAktivitet);