<?php

use UKMNorge\Statistikk\Objekter\StatistikkArrangement;
use UKMNorge\OAuth2\ArrSys\HandleAPICallWithAuthorization;


$tilgang = 'superadmin'; // Kreves tilgang som superadmin for å se statistikk for alle kommuner
$tilgangAttribute = null; 

$handleCall = new HandleAPICallWithAuthorization([], [], ['POST'], false, false, $tilgang, $tilgangAttribute);


$handleCall->sendToClient(StatistikkArrangement::getAntallArrangementTyperLokalt());