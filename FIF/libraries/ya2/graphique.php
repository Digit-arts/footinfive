<?php

set_include_path(get_include_path().PATH_SEPARATOR.JPATH_BASE.'/libraries/ya2');
	
require_once('libchart/libchart/classes/libchart.php');
	
	
$chemin="/var/www/vhosts/footinfive.com/httpdocs/FIF/libraries/ya2/Graphs-freq/";
$nom_fichier="pif.png";
	$chart = new LineChart();

	$dataSet = new XYDataSet();
	$dataSet->addPoint(new Point("06-01", 273));
	$dataSet->addPoint(new Point("06-02", 421));
	$dataSet->addPoint(new Point("06-03", 642));
	$dataSet->addPoint(new Point("06-04", 799));
	$dataSet->addPoint(new Point("06-05", 1009));
	$dataSet->addPoint(new Point("06-06", 1406));
	$dataSet->addPoint(new Point("06-07", 1820));
	$dataSet->addPoint(new Point("06-08", 2511));
	$dataSet->addPoint(new Point("06-09", 2832));
	$dataSet->addPoint(new Point("06-10", 3550));
	$dataSet->addPoint(new Point("06-11", 4143));
	$dataSet->addPoint(new Point("06-12", 4715));
	$chart->setDataSet($dataSet);

	$chart->setTitle("pof");
	$chart->render($chemin.$nom_fichier);
?>
<img alt="Line chart" src="<? echo $chemin.$nom_fichier; ?>" style="border: 1px solid gray;"/>

