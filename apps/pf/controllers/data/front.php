<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\pf\controllers\data;

use \timer as timer;
use \apps\pf\models as models;
use \models\user as user;


class front extends data {
	function __construct() {
		parent::__construct();

	}


	function _pages($pID="",$dID="") {

		$user = $this->f3->get("user");
		$userID = $user['ID'];
		
		
		if (!$pID) {
			$pID = $user['publication']['ID'];
		}
		if (!$dID) {
			
			$dID = isset($_GET['dID'])?$_GET['dID']:"";
		}

		$dateList = \models\dates::getAll("pID = '$pID'");

		
	
		$pdf_dID = $this->f3->get("DB")->exec("SELECT dID FROM `global_pages` WHERE pdf is not null AND pID ='$pID' GROUP BY dID ORDER BY dID DESC");

		$n = array();
		foreach ($pdf_dID as $item){
			$n[] = $item['dID'];
		}
		$pdf_dID = $n;
		
		if (!$dID) {
			if (isset($pdf_dID[0])){
				$dID = $pdf_dID[0];
			} else {
				$dID = $dateList[0]['ID'];
			}
			
		}

		
		$n = array();
		foreach ($dateList as $item){
			$item['has'] = in_array($item['ID'],$pdf_dID)?"1":"0";
			$n[] = $item;
		}
		$dateList = $n;
		
		//test_array($dateList); 
		


		$settings = models\settings::_read("front");

		$defaults = $this->f3->get("defaults");

		
		
		

		$highlight = isset($_GET['highlight'])? $_GET['highlight']: $settings['highlight'];
		if (!$highlight){
			$highlight = $defaults['overview']['highlight'];
		}


		$zoom = $settings['zoom'];
		if (!$zoom){
			$zoom = $defaults['front']['zoom'];
		}
		$zoom_change = isset($_GET['zoom'])? $_GET['zoom']: "";

		if ($zoom_change){
			$zoom = $zoom + $zoom_change;
		}
		$values = array();
		$values["front"] = array(
			"highlight"    => $highlight,
			"zoom"    => $zoom,
		);


		models\settings::save($values);
		
		
		//test_array($zoom); 

	

		$editionPages = $this->_num_pages($dID);



		$blank = array(
			"page"   => 0,
			"pdf"   => null,
			"locked"   => 0,
			"highlight"   => 0,
			"section"=> array(
				"n"=> "",
				"c"=> "",

			),
			"colour" => "",
		);
		
		
		$pagesReal = models\pages::getAll("global_pages.pID='$pID' AND global_pages.dID = '$dID'","page ASC");

		$colourGroups = array();
		foreach ($user['publication']['colours_group'] as $g) {
			$colourGroups[$g['ID']] = $g;
		}


		$r = array();
		foreach ($pagesReal as $page){
			$records = isset($bookings[$page['ID']]) ? $bookings[$page['ID']] : array();
			
			$h = "";

			


			$colour = array(
				"heading"=>"",
				"limit"=>"",
				"icons"=>"",
			);
			if ($page['colourID']){
				if (isset($colourGroups[$page['colourID']])){
					$colour = array(
						"heading"=> $colourGroups[$page['colourID']]['label'],
						"icons"=> strtolower(str_replace(array(" ","&","_"),"",$colourGroups[$page['colourID']]['label'])),
						"limit"=> $colourGroups[$page['colourID']]['colour_string'],
					);
				}


			}





			$r[$page['page']] = array(
				"page"   => $page['page'],
				"pdf"   => $page['pdf'],
				"dID"   => $page['dID'],
				"pID"   => $page['pID'],
				"cID"   => $page['cID'],
				"highlight"   => $h,
				"section"=> array(
					"i"=>$page['sectionID'],
					"n"=>$page['section'],
					"c"=>$page['section_colour']
				),
				"colour" => $colour,
			);
		}

		$pages = array();
		for ($i = 1; $i <= $editionPages; $i++) {
			$p = $blank;
			$p['page'] = $i;
			if (isset($r[$i])){
				$pages[] = $r[$i];
			} else {
				$pages[] = $p;
			}

		}

		$pagesCount = count($pages);



		$spreads = ($pagesCount / 2) + 1;


		$spread = array();

		$h = 0;
		for ($i = 0; $i < $spreads; $i++) {
			$lh = $h++;
			if (isset($pages[$lh])) {
				$page = $pages[$lh];
				$page['side'] = "left";
				//if (isset($spread[$i])) {
				$spread[$i]['pages'][] = $page;
				if ($i > 0) {
					if (isset($pages[$h])) {
						$page = $pages[$h++];
						$page['side'] = "right";

						$spread[$i]['pages'][] = $page;
					}
				}
				$spread[$i]['side'] = (strpos($i / 2, ".")) ? "right" : "left";
				$spread[$i]['index'] = $i;
				//}
			}


		}

		if (count(($spread))) $spread[count($spread) - 1]['pages'][0]['side'] = "right";
		$a = array();
		foreach ($spread as $b) $a[] = $b;
		$spread = $a;

		$pages = array();

		$pages["spreads"] = $spread;
		$pages["count"] = $pagesCount;


		$size = "";
		$plus = true;
		$minus = true;
		switch($zoom){
			case "1":
				$size = "61";
				$minus = false;
				break;
			case "2":
				$size = "76";
				break;
			case "3":
				$size = "97";
				break;
			case "4":
				$size = "137";
				break;
			case "5":
				$size = "213";
				break;
			case "6":
				$size = "415";
				$plus = false;
				break;
			default:
				$zoom = "3";
				$size = "97";

		}



		$date = new \models\dates();
		$date = $date->get($dID);


		$return = $pages;
		$return['zoom'] = array(
			"p"=>$plus,
			"m"=>$minus,
			"z"=>$zoom
		);
		$return['size'] = $size;
		$return['highlight'] = $highlight;
		$return['dID'] = $dID;
		$return['date'] = date("d M Y",strtotime($date['publish_date_display']));
		$return['datelist'] = $dateList;
		
		
		
		//test_array($return); 

		return $GLOBALS["output"]['data'] = $return;
	}
	function _num_pages($dID){

		$date = new \models\dates();
		$date = $date->get($dID);
		
		$pID = $date['pID'];
		$dID = $date['ID'];
		
		
		$data = \apps\ab\models\bookings::getAll("(ab_bookings.pID = '$pID' AND ab_bookings.dID='$dID') AND ab_bookings.deleted is null AND typeID='1' ");
		$statsData = array();
		$layoutcm = 0;
		foreach ($data as $item){
			$layoutcm = $layoutcm + $item['totalspace'];
			if ($item['checked']=='1') $statsData[] = $item;
		}
		
		
		//test_array($date); 
		
		
		$loading = \apps\ab\models\loading::getLoading($pID, $layoutcm, $date['pages']);
		
		
		
		
		return $loading['pages'];
		
	}
	


	

	function _details(){
		$page_nr = (isset($_REQUEST['page'])) ? $_REQUEST['page'] : "";
		$user = $this->f3->get("user");
		$userID = $user['ID'];

		$pID = $user['publication']['ID'];

		$dID = $user['publication']['current_date']['ID'];

		$page = models\pages::getAll("page='$page_nr' AND global_pages.dID = '$dID' AND global_pages.pID='$pID'");

		if (count($page)) {
			$page = $page[0];
		} else {
			$page = models\pages::dbStructure();
			$page['page'] = $page_nr;
		}


		$GLOBALS["output"]['data'] = $page;
	}
	

}
