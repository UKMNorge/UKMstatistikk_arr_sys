<?php
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Database\SQL\Query;
require_once('UKM/Autoloader.php');

$query = new Query(
    "SELECT pl.pl_id, pl_name, pl_subtype, rel_b_p.p_id, pl.season
    FROM smartukm_place as pl 
    JOIN smartukm_rel_pl_b AS rel_pl_b ON rel_pl_b.pl_id=pl.pl_id 
    JOIN smartukm_rel_b_p AS rel_b_p ON rel_b_p.b_id=rel_pl_b.b_id 
    WHERE pl.season = 2025"
);

$res = $query->run();

$arrangementer = [];
while ($r = Query::fetch($res)) {
    $arrObj = new Arrangement($r['pl_id']);
    $arrangementer[$r['pl_id']]['pl_id'] = $r['pl_id'];
    $arrangementer[$r['pl_id']]['navn'] = $r['pl_name'];
    $arrangementer[$r['pl_id']]['season'] = $r['season'];
    $arrangementer[$r['pl_id']]['type'] = $r['pl_subtype'];
    $arrangementer[$r['pl_id']]['antall'] = $arrObj->getAntallPersoner();
    $arrangementer[$r['pl_id']]['omrade'] = $arrObj->getType();
    $arrangementer[$r['pl_id']]['kommuner'] = $arrObj->getKommuner();
    $arrangementer[$r['pl_id']]['obj'] = $arrObj;
}

echo '<table border="1" cellpadding="5" cellspacing="0">';
echo '<tr>
        <th>Counter</th>
        <th>Arrangement ID</th>
        <th>Navn</th>
        <th>Sesong</th>
        <th>Type</th>
        <th>Antall</th>
        <th>Område</th>
        <th>Kommuner</th>
        <th>URL</th>
        </tr>';
// Sorter arrangementer etter høyest antall
usort($arrangementer, function($a, $b) {
    return $b['antall'] <=> $a['antall'];
});

$arr = [];
$arr['<10'] = 0;
$arr['11-20'] = 0;
$arr['21-50'] = 0;
$arr['51-100'] = 0;
$arr['100+'] = 0;

$counter = 0;
foreach ($arrangementer as $pl_id => $arrangement) {
    // if($arrangement['type'] != 'monstring') {
    //     continue;
    // }
    $counter++;
    if($arrangement['omrade'] != 'kommune') {
        continue;
    }

    if($arrangement['antall'] == 0) {
        continue;
    }

    try{
        $kommune = $arrangement['obj']->getKommune();

        if($kommune->getFylke()->erFalskt()) {
            continue;
        }
    } catch( Exception $e ) {
        // DO nothing, flerekommuner ble truffet
    }

    $antPersoner = $arrangement['antall'];

    if($antPersoner < 11) {
        $arr['<10'] = $arr['<10']+1;
    }
    elseif($antPersoner < 21) {
        $arr['11-20'] = $arr['11-20']+1;
    }
    else if($antPersoner < 51) {
        $arr['21-50'] = $arr['21-50']+1;
    }
    else if($antPersoner < 101) {
        $arr['51-100'] = $arr['51-100']+1;
    }
    else {
        $arr['100+'] = $arr['100+']+1;
    }

    echo '<tr>';
    echo '<td>' . $counter . '</td>';
    echo '<td>' . htmlspecialchars($arrangement['pl_id']) . '</td>';
    echo '<td>' . htmlspecialchars($arrangement['navn']) . '</td>';
    echo '<td>' . htmlspecialchars($arrangement['season']) . '</td>';
    echo '<td>' . htmlspecialchars($arrangement['type']) . '</td>';
    echo '<td>' . htmlspecialchars($arrangement['antall']) . '</td>';
    echo '<td>' . htmlspecialchars($arrangement['omrade']) . '</td>';
    echo '<td>' . htmlspecialchars($arrangement['kommuner']) . '</td>';
    echo '<td><a href="//ukm.no/' . $arrangement['obj']->getPath() . '/wp-admin" target="_blank">Link</a></td>';
    echo '</tr>';
}
echo '</table>';

echo '<pre>';
var_dump($arr);
echo '</pre>';
