<?php

use UKMNorge\OAuth2\ArrSys\HandleAPICallWithAuthorization;
use UKMNorge\Geografi\Kommune;
use UKMNorge\Statistikk\Objekter\StatistikkKommune;


$tilgang = 'kommune_eller_fylke'; // Er admin i minst 1 kommune eller 1 fylke
$tilgangAttribute = null; // Sender null, trenger ikke dette fordi det sjekkes kun generelt tilgang

$handleCall = new HandleAPICallWithAuthorization(['season'], [], ['GET', 'POST'], false, false, $tilgang, $tilgangAttribute);

$season = $handleCall->getArgument('season');

$handleCall->sendToClient(["gjennomsnitt" => StatistikkKommune::gjennomsnittDeltakereIKommuner($season)]);