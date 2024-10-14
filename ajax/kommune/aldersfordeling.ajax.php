<?php

use UKMNorge\OAuth2\ArrSys\HandleAPICallWithAuthorization;
use UKMNorge\Geografi\Kommune;
use UKMNorge\Statistikk\Objekter\StatistikkKommune;


$kommuneId = HandleAPICallWithAuthorization::getArgumentBeforeInit('kommuneId', 'POST');

if($kommuneId == null) {
    HandleAPICallWithAuthorization::sendError('Mangler kommuneId', 400);
}

$tilgang = 'kommune_eller_fylke'; // Er admin i kommune eller fylke kommunen er del av
$tilgangAttribute = $kommuneId; // Er admin i kommune med id $kommuneId

$handleCall = new HandleAPICallWithAuthorization(['kommuneId', 'season'], [], ['GET', 'POST'], false, false, $tilgang, $tilgangAttribute);

$kommuneId = $handleCall->getArgument('kommuneId');
$season = $handleCall->getArgument('season');

$kommune = null;
try{
    $kommune = new Kommune($kommuneId);
} catch(Exception $e) {
    if($e->getCode() == 401) {
        $handleCall->sendErrorToClient($e->getMessage(), 401);
    }
    $handleCall->sendErrorToClient('Kunne ikke hente kommunen', 500);
}

$statKom = null;
try{
    $statKom = new StatistikkKommune($kommune, $season);
}
catch(Exception $e) {
    $handleCall->sendErrorToClient('Kunne ikke hente statistikk for kommune', 401);

}

$handleCall->sendToClient($statKom->getAldersfordeling());