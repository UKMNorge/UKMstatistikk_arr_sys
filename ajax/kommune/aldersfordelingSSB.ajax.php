<?php

use UKMNorge\Statistikk\Objekter\StatistikkSSB;
use UKMNorge\OAuth2\HandleAPICall;

$handleCall = new HandleAPICall(['kommuneId', 'year'], [], ['GET', 'POST'], false);

$kommuneId = $handleCall->getArgument('kommuneId');
$year = $handleCall->getArgument('year');


$handleCall->sendToClient(StatistikkSSB::getAldersfordelingKommune($kommuneId, $year));

