<?php

use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Statistikk\Objekter\StatistikkArrangement;

$handleCall = new HandleAPICall([], [], ['POST'], false);


$handleCall->sendToClient(StatistikkArrangement::getAntallArrangementTyperLokalt());