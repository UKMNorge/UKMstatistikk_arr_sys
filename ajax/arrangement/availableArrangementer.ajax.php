<?php

use UKMNorge\OAuth2\ArrSys\HandleAPICallWithAuthorization;
use UKMNorge\Nettverk\Omrade;

use Exeption;

$tilgang = ''; // Tilgang til alle for å hente arrangement id og navn
$tilgangAttribute = null;

$handleCall = new HandleAPICallWithAuthorization(['omradeId', 'omradeType'], [], ['GET', 'POST'], false, false, $tilgang, $tilgangAttribute);

$omradeId = $handleCall->getArgument('omradeId');
$omradeType = $handleCall->getArgument('omradeType');

if($omradeType != 'fylke' && $omradeType != 'kommune') {
    $handleCall->sendErrorToClient('Ugyldig områdetype: Kan kun hente kjonnsfordeling for fylke eller kommune', 400);
}

$omrade = new Omrade($omradeType, $omradeId);

$retArr = [];

foreach($omrade->getArrangementer()->getAll() as $arrangement) {
    $retArr[] = [ 
        'id' => $arrangement->getId(),
        'navn' => $arrangement->getNavn(),
        'sesong' => $arrangement->getSesong(),
    ];
}

$handleCall->sendToClient($retArr);
