<?php
/*
 * Date: 2012/02/27
 * Time: 2:27 PM
 */

class charts {

	function line(){
		$MyData = new pData();
		$title = (isset($_GET['title'])) ? $_GET['title'] : "";
	$b = (isset($_GET['b']))?explode(",",$_GET['b']):"";

	$w = (isset($_GET['w']))?$_GET['w']:700;
	$h = (isset($_GET['h']))?$_GET['h']:230;
	$y_label = (isset($_GET['y']))?$_GET['y']:"";

		if ($y_label){
			$x_offset = 40;
		} else {
			$x_offset = 15;
		}

		$y_offset = 0;

	$x_labels = (isset($_GET['l'])) ? explode(",",$_GET['l']) : "";

		$d = array();

		foreach($_GET as $key=>$value){
			if (substr($key,0,4)=="data"){

				$value = explode("|", $value);

					$data = array(
						"data"  => str_replace("_", VOID, explode(",", $value[0])),
						"legend"=> (isset($value[1])) ? $value[1] : "",
						"weight"=> (isset($value[2])) ? $value[2] : 1
					);
					$d[] = $data;

			}
		}


	$labelSkip = 0;
	$labels = count($x_labels);

	$chartarea = $w -30;

	$spaces = $chartarea / 25;

	$labelSkip = floor($labels / $spaces);

;



		/* Create and populate the pData object */
	foreach ($d as $line){

		$MyData->addPoints($line['data'], $line['legend']);
		if ($line['weight']) {
			$MyData->setSerieWeight($line['legend'], $line['weight']);
		}
	}


 if ($y_label) {
	 $MyData->setAxisName(0, $y_label);
	 $MyData->setAxisDisplay(0);
 }

if ($x_labels) {
	$MyData->addPoints($x_labels,"Labels");
	$MyData->setSerieDescription("Labels", "Months");

}

 $MyData->setAbscissa("Labels");


 /* Create the pChart object */
 $myPicture = new pImage($w,$h,$MyData);

 /* Turn of Antialiasing */
 $myPicture->Antialias = FALSE;

		if ($b){
			/* Add a border to the picture */
 $myPicture->drawRectangle(0,0,$w-1,$h-1,array("R"=>$b[0]*1,"G"=> $b[0]*1,"B"=> $b[0]*1));
		}

if ($title){
 /* Write the chart title */
 $myPicture->setFontProperties(array("FontName"=>"./lib/pChart/fonts/Forgotte.ttf","FontSize"=>11));
 $myPicture->drawText(30,10, $title,array("FontSize"=>20,"Align"=>TEXT_ALIGN_TOPLEFT));
	$y_offset = 30;
}

 /* Set the default font */
 $myPicture->setFontProperties(array("FontName"=>"./lib/pChart/fonts/pf_arma_five.ttf","FontSize"=>6));

 /* Define the chart area */
 $myPicture->setGraphArea($x_offset, $y_offset,$w-10,$h-30);

 /* Draw the scale */
 $scaleSettings = array("XMargin"=>10,"YMargin"=>10,"Floating"=>TRUE,"GridR"=>200,"GridG"=>200,"GridB"=>200,"CycleBackground"=>TRUE,"LabelRotation"=>0,"Mode"=> SCALE_MODE_START0,"LabelSkip"=> $labelSkip,"LabelingMethod"=> LABELING_ALL,"DrawSubTicks"=>false);
 $myPicture->drawScale($scaleSettings);
			//drawScale($Data, $DataDescription, $ScaleMode, $R, $G, $B, $DrawTicks = TRUE, $Angle = 0, $Decimals = 1, $WithMargin = FALSE, $SkipLabels = 1, $RightScale = FALSE)

 /* Turn on Antialiasing */
 $myPicture->Antialias = TRUE;

 /* Draw the line chart */
 $myPicture->drawLineChart(array("DisplayValues"=>TRUE,"DisplayColor"=>array("R"=>0,"G"=> 0,"B"=> 0),"PlotBorder"=>TRUE,"BorderSize"=>2,"Surrounding"=>-60,"BorderAlpha"=>80));

// $myPicture->drawPlotChart(array("DisplayValues"=>TRUE,"PlotBorder"=>TRUE,"BorderSize"=>1,"Surrounding"=>-40,"BorderAlpha"=>80));

 /* Write the chart legend */
 $myPicture->drawLegend($x_offset,$h-10,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));


/* hide the verticle labels */
	$myPicture->drawFilledRectangle(0, 0, $x_offset+2, $h - 35, array("R" => 250, "G" => 250, "B" => 250,"Alpha"=>100));
 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawLineChart.simple.png");



exit();
	}
}
