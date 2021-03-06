<?php
/* Statistik für Grün/Rot */
    $databaseGR = new Database(KOM::$dblink);
    $databaseGR->setFilter("parties", array(1,2));
    $databaseGR->loadContent();

    $auswGR = new Analysis($databaseGR);

    /* Aktuelle Verteilung */
    $nrGR = $auswGR->getCurrentNumberOfPledgestatetypes();
    
	$group_nrGR = array();
	
    foreach ($databaseGR->getPledgestatetypegroups() as $value0) {
        foreach ($databaseGR->getPledgestatetypegroup($value0->getID())->getPledgestatetypes() as $value) {
			if (!isset($group_nrGR[$value0->getID()])) {
				$group_nrGR[$value0->getID()] = 0;
			}
            $group_nrGR[$value0->getID()] += $nrGR[$value->getID()];
        }
    }

    

/* Statistik für die Koalition */

    $databaseK = new Database(KOM::$dblink);
    $databaseK->setFilter("parties", array(3));
    $databaseK->loadContent();

    $auswK = new Analysis($databaseK);
    
    
    /* Aktuelle Verteilung */
    $nrK = $auswK->getCurrentNumberOfPledgestatetypes();
    $group_nrK = array();
    foreach ($databaseK->getPledgestatetypegroups() as $value0) {
        foreach ($databaseK->getPledgestatetypegroup($value0->getID())->getPledgestatetypes() as $value) {
			if (!isset($group_nrK[$value0->getID()])) {
				$group_nrK[$value0->getID()] = 0;
			}
			if (isset($nrK[$value->getID()])) {
				$group_nrK[$value0->getID()] += $nrK[$value->getID()];
			}
        }
    }

    /* Diagramm erstellen */
        $chart = new sto_highchart_parser("GR_verteilung");
        $chart->options['title']['text'] = "";
        $chart->options['chart']['plotShadow'] = true;
        $chart->options['plotOptions']['pie']['dataLabels']['enabled'] = false;
        $chart->options['plotOptions']['pie']['dataLabels']['color'] = "#f00";
        $chart->options['plotOptions']['pie']['dataLabels']['connectorColor'] = "#00f";
        $chart->options['plotOptions']['pie']['animation'] = false;
        $chart->options['plotOptions']['pie']['shadow'] = true;
        //$chart->activateLinks();
        
        $chart->options['series'] = $auswGR->getChartseriesPieGroup(false, array(2 => array("sliced" => true)));
    
    /* Diagramm erstellen */
            
        $chart2 = new sto_highchart_parser("GR_verlauf");
        $chart2->options['chart']['type'] = "area";
        $chart2->options['title']['text'] = "";
        $chart2->options['plotOptions']['area']['stacking'] = "normal";
        $chart2->options['plotOptions']['area']['trackByArea'] = true;
        $chart2->options['plotOptions']['area']['marker']['enabled'] = false;
        $chart2->options['plotOptions']['area']['marker']['symbol'] = "circle";
        $chart2->options['plotOptions']['area']['animation'] = false;
        $chart2->options['plotOptions']['area']['fillOpacity'] = 0.5;
        $chart2->options['xAxis']['max'] = $databaseGR->getOption("end_datum")."000";
        $chart2->options['xAxis']['type'] = "datetime";
        $chart2->options['yAxis']['min'] = 0;
        $chart2->options['yAxis']['endOnTick'] = false;
        $chart2->options['yAxis']['title']['text'] = "";
        $chart2->activateLinks("custom", "http://www.google.de");
        $chart2->options['series'] = $auswGR->getChartseriesTrendGroup();

    
?>



<? include('templates/home.php'); ?>

<script type="text/javascript">
<?
echo $chart->render();
echo $chart2->render();

?>
</script>
