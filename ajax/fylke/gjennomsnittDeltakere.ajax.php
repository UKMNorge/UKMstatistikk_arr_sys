<?php

use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Database\SQL\Query;
use UKMNorge\Geografi\Fylke;
use UKMNorge\Geografi\Fylker;
use UKMNorge\Statistikk\Objekter\StatistikkFylke;



// Det brukes POST fordi WP tillater POST bare
$handleCall = new HandleAPICall(['plId'], [], ['GET', 'POST'], false);
$plId = $handleCall->getArgument('plId');

$fylke = Fylker::getById(1);

$statArr = new StatistikkFylke($fylke, 2010);


echo('Gjennomsnitt 2011: ');
echo($statArr->getGjennomsnittDeltakere(2011));


