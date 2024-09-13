<?php

use UKMNorge\Statistikk\Objekter\StatistikkArrangement;
use UKMNorge\Statistikk\StatistikkHandleAPICall;


$tilgang = 'superadmin'; // Kreves tilgang som superadmin for å se statistikk for alle kommuner
$tilgangAttribute = null; 

$handleCall = new StatistikkHandleAPICall([], [], ['POST'], false, false, $tilgang, $tilgangAttribute);


$handleCall->sendToClient(['antall' => StatistikkArrangement::getAntallArrangementer()]);