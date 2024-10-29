<?php
use UKMNorge\Geografi\Kommune;
use UKMNorge\API\SSB\Klass;
use UKMNorge\Database\SQL\Insert;

$kommuneId = 4204; // Kristiansand

$kommune = new Kommune($kommuneId);
$tidligereKommuner = $kommune->getTidligereKommuner();

foreach($tidligereKommuner as $kommune) {
    echo $kommune->getId().": ".$kommune->getNavn()."<br>";
    var_dump($kommune->getActiveYears());
    echo '<br>';
}


die('not allowed');
$allYears = [2009, 2010, 2011, 2012, 2013, 2014, 2015, 2016, 2017, 2018, 2019, 2020, 2021, 2022, 2023, 2024];

foreach($allYears as $season) {
    echo "<h1>".$season."</h1>";
    // Hent alle kommuner fra SSB
    $dataset = new Klass();
    // 131 er "Standard for kommuneinndeling"
    $dataset->setClassificationId("131");
    $startDato = new DateTime($season."-01-01");
    $sluttDato = new DateTime($season."-12-31");
    
    $dataset->setRange($startDato, $sluttDato);
    $dataset->includeFutureChanges(true);
    $kommuner = $dataset->getCodes();
    
    $retKommuner = [];
    foreach($kommuner->codes as $kommune) {
        try{
            $kommune = new Kommune($kommune->code);
        }catch(Exception $e) {
            continue;
        }
        
        if($kommunerIFylke && $kommune->getFylke() && $kommune->getFylke()->getId() == $kommunerIFylke->getId()) {
            $retKommuner[$kommune->getId()] = $kommune;
        }else if(!$kommunerIFylke) {
            $retKommuner[$kommune->getId()] = $kommune;
        }
        
    }
    
    foreach($retKommuner as $kommune) {
        echo $kommune->getId().": ".$kommune->getNavn()."<br>";
        // DB
        $query = new Insert('ukm_kommune_year');
        $query->add('year', $season);
        $query->add('k_id', $kommune->getId());
        try {
            $res = $query->run();
            var_dump($res);
        } catch ( Exception $e ) {
            if( $e->getCode() == 901001 ) {
                error_log($query->debug());
            }
        }
    }
}
