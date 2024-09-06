<?php

// Alle kommuner i hele landet som har UKM-aktivtet (relatert til antall kommuner totalt)

use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Geografi\Fylker;
use UKMNorge\Statistikk\Objekter\StatistikkKommune;


$handleCall = new HandleAPICall(['season'], [], ['GET', 'POST'], false);
$season = $handleCall->getArgument('season');


$$kommunerAktivitet = null;
try{
    $kommunerAktivitet = StatistikkKommune::getAlleKommunerAktivitet($season);
} catch(Exception $e) {
    $handleCall->sendErrorToClient('Kunne ikke hente statistikk for kommune', 401);
}

$handleCall->sendToClient($kommunerAktivitet);