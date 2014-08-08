<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\ab\controllers\data;

use \timer as timer;
use \apps\ab\models as models;
use \models\user as user;


class overview extends data {
	function __construct() {
		parent::__construct();

	}


	function _pages() {

		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];

		$settings = models\settings::_read("overview");

		$defaults = $this->f3->get("defaults");

		$currentDate = $user['publication']['current_date'];
		$dID = $currentDate['ID'];
		$bookingsRaw = models\bookings::getAll("(ab_bookings.pID = '$pID' AND ab_bookings.dID='$dID') AND checked = '1' AND ab_bookings.deleted is null AND checked = '1' AND typeID='1' ", "client ASC");
		$stats = $this->_stats();

		$highlight = isset($_GET['highlight'])? $_GET['highlight']: $settings['highlight'];
		if (!$highlight){
			$highlight = $defaults['overview']['heighlight'];
		}


		$zoom = $settings['zoom'];
		if (!$zoom){
			$zoom = $defaults['overview']['heighlight'];
		}
		$zoom_change = isset($_GET['zoom'])? $_GET['zoom']: "";
		
		if ($zoom_change){
			$zoom = $zoom + $zoom_change;
		}
		
		

		$editionPages = $stats['loading']['pages'];


		$values = array();
		$values["overview"] = array(
			"highlight"    => $highlight,
			"zoom"    => $zoom,
		);


		models\settings::save($values);
		//test_array($usersettings);



		$statsBlank = array(
			"records"=> array(
				"total"            => 0,
				"material"         => array(
					"y"=> 0,
					"n"=> 0,
					"p"=>0,
					"t"=>0
				),
				"material_approved"=> array(
					"y"=> 0,
					"n"=> 0,
					"p" => 0,
					"t" => 0
				),
				"planned"         => array(
					"y"=> 0,
					"n"=> 0,
					"p"=>0,
					"t"=>0
				),
				"bar"=> array(
					"y" => 0,
					"n" => 0,
					"p" => 0,
					"t" => 0
				),
			)

		);

		$blank = array(
			"page"   => 0,
			"locked"   => 0,
			"highlight"   => 0,
			"section"=> array(
				"n"=> "",
				"c"=> "",

			),
			"colour" => "",
			"stats"=> $statsBlank
		);


		//test_array($bookingsRaw); 


		$bookings = array();
		foreach ($bookingsRaw as $booking){
			if ($booking['pageID']) {
				$a = array();
				$a['ID'] = $booking['ID'];
				$a['client'] = $booking['client'];
				$a['colour'] = $booking['colour'];
				$a['col'] = $booking['col']+0;
				$a['cm'] = $booking['cm']+0;
				$a['totalspace'] = $booking['totalspace']+0;
				$a['pageID'] = $booking['pageID'];
				$a['page'] = $booking['page'];
				$a['material'] = $booking['material'];
				$a['material_approved'] = $booking['material_approved'];
				$a['planned'] = $booking['planned'];
				$a['material_file_store'] = $booking['material_file_store'];
				$a['x_offset'] = $booking['x_offset']?$booking['x_offset']+0:"";
				$a['y_offset'] = $booking['y_offset']?$booking['y_offset']+0:"";

				$bookings[$booking['pageID']][] = $a;
			}
		}



		$pagesReal = models\pages::getAll("global_pages.pID='$pID' AND global_pages.dID = '$dID'","page ASC");

		$colourGroups = array();
		foreach ($user['publication']['colours_group'] as $g) {
			$colourGroups[$g['ID']] = $g;
		}


		$r = array();
		foreach ($pagesReal as $page){
			$records = isset($bookings[$page['ID']]) ? $bookings[$page['ID']] : array();
			$stats = $statsBlank;
			foreach ($records as $record){
				if ($record['material']){
					$stats['records']['material']['y']++;
					if ($record['material_approved']){
						$stats['records']['material_approved']['y']++;
					} else {
						$stats['records']['material_approved']['n']++;
					}
				} else {
					$stats['records']['material']['n']++;
				}
				if ($record['planned']){
					$stats['records']['planned']['y']++;
					
				} else {
					$stats['records']['planned']['n']++;
				}
				
				


			}
			$stats['records']['records'] = $records;
			$stats['records']['total']=count($records);
			$h = "";

			if ($stats['records']['total'] && $stats['records']['material']['y']) {
				$stats['records']['material']['p'] = number_format(($stats['records']['material']['y'] / $stats['records']['total']) * 100, 2);
			}
			if ($stats['records']['total'] && $stats['records']['planned']['y']) {
				$stats['records']['planned']['p'] = number_format(($stats['records']['planned']['y'] / $stats['records']['total']) * 100, 2);
			}
			
			
			
			$stats['records']['material']['t'] = $stats['records']['total'];
			$stats['records']['planned']['t'] = $stats['records']['total'];
			
			
			if ($stats['records']['material']['t'] && $stats['records']['material_approved']['y']) {
				$stats['records']['material_approved']['p'] = number_format(($stats['records']['material_approved']['y'] / $stats['records']['material']['y']) * 100, 2);
			}


			$stats['records']['material_approved']['t'] = $stats['records']['material']['y'];


			switch ($highlight){
				case "material":
					if ($stats['records']['material']['n']) $h = "no";
					if (!$stats['records']['material']['n'] && $stats['records']['material']['y']) $h = "yes";
					$stats['records']['bar'] = $stats['records']['material'];

					break;
				case "material_approved":
					if ($stats['records']['material_approved']['n']) $h = "no";
					if (!$stats['records']['material_approved']['n'] && $stats['records']['material_approved']['y']) $h = "yes";
					$stats['records']['bar'] = $stats['records']['material_approved'];
					break;
				case "planned":
					if ($stats['records']['planned']['n']) $h = "no";
					if (!$stats['records']['planned']['n'] && $stats['records']['planned']['y']) $h = "yes";
					$stats['records']['bar'] = $stats['records']['planned'];

					break;
				
				case "locked":
					if ($page['locked']=='1') $h = "no";
					$stats['records']['bar'] = array(
						"y" => 0,
						"n" => 0,
						"p" => 0
					);
					break;
			}


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
				"locked"   => $page['locked'],
				"highlight"   => $h,
				"section"=> array(
					"i"=>$page['sectionID'],
					"n"=>$page['section'],
					"c"=>$page['section_colour']
				),
				"colour" => $colour,
				"percent"=> $page['percent'],
				"cm"     => $page['cm'],
				//"records"=> $records,
				"stats"=>$stats
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


	//test_array($pages);




		$pagesCount = count($pages);



		$spreads = ($pagesCount / 2) + 1;


		$t = array(
			"count"  => $pagesCount,
			"spreads"=> $spreads
		);


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
		
		
		
		
		

		$return = $pages;
		$return['zoom'] = array(
			"p"=>$plus,
		    "m"=>$minus,
		    "z"=>$zoom
		);
		$return['size'] = $size;
		$return['date'] = $currentDate['publish_date_display'];
		$return['dID'] = $currentDate['ID'];
		$return['stats'] = $stats;

		return $GLOBALS["output"]['data'] = $return;
	}


	function _page($page=""){
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];

		$page = ($page)?$page: isset($_REQUEST['page'])?$_REQUEST['page']:"";

		$currentDate = $user['publication']['current_date'];
		$dID = $currentDate['ID'];


		$blank = array(
			"page"   => 0,
			"section"=> array(
				"n"=> "",
				"c"=> "",

			),
			"colour" => "",
			"percent"=>0,
			"cm"=>0

		);




		$pagesReal = models\pages::getAll("page='$page' AND global_pages.pID='$pID' AND global_pages.dID = '$dID'", "page ASC, ID DESC");

		$page = $pagesReal[0];


		$r = array(
				"page"   => $page['page'],
				"section"=> array(
					"i"=> $page['sectionID'],
					"n"=> $page['section'],
					"c"=> $page['section_colour']
				),
				"colour" => $page['colour'],
				"percent"=> $page['percent'],
				"cm"     => $page['cm']
			);


		$pageID = $page['ID'];
		$bookingsRaw = models\bookings::getAll("(ab_bookings.pID = '$pID' AND ab_bookings.dID='$dID') AND checked = '1' AND ab_bookings.deleted is null AND typeID='1'", "client ASC");
		$bookings = array();
		foreach ($bookingsRaw as $booking) {
			if ($booking['pageID'] == $pageID) {
				$a = array();
				$a['ID'] = $booking['ID'];
				$a['client'] = $booking['client'];
				$a['colour'] = $booking['colour'];
				$a['colourSpot'] = $booking['colourSpot'];
				$a['col'] = $booking['col'];
				$a['cm'] = $booking['cm'];
				$a['totalspace'] = $booking['totalspace'];
				$a['pageID'] = $booking['pageID'];
				$a['page'] = $booking['page'];
				$a['material'] = $booking['material'];
				$a['material_approved'] = $booking['material_approved'];
				$a['planned'] = $booking['planned'];

				$bookings[] = $a;
			}
		}

		$r['records'] = $bookings;

		$r['stats'] = $this->_stats();;


		$return = $r;
		$return['date'] = $currentDate['publish_date_display'];
		$return['dID'] = $currentDate['ID'];

		return $GLOBALS["output"]['data'] = $return;
	}
	function _stats($data="") {
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];

		$currentDate = $user['publication']['current_date'];
		$dID = $currentDate['ID'];


		if (!is_array($data)) {
			$data = models\bookings::getAll("(ab_bookings.pID = '$pID' AND ab_bookings.dID='$dID') AND ab_bookings.deleted is null AND typeID='1' ");
			$statsData = array();
			$layoutcm = 0;
			foreach ($data as $item){
				$layoutcm = $layoutcm + $item['totalspace'];
				if ($item['checked']=='1') $statsData[] = $item;
			}
			$loading = models\loading::getLoading($pID, $layoutcm, $currentDate['pages']);
			$stats = models\record_stats::stats($statsData,array("cm","placed","placed_cm","planned"), $loading['pages']);
			$stats['loading'] = $loading;

		} else {
			$stats = models\record_stats::stats($data,array("cm"));
			$loading = models\loading::getLoading($pID, $stats['cm'], $currentDate['pages']);
			$stats = models\record_stats::stats($data,array("cm","placed","placed_cm","planned"), $loading['pages']);
			$stats['loading'] = $loading;
		}




		return $GLOBALS["output"]['data'] = $stats;

	}

	function _details_page(){
		$page_nr = (isset($_REQUEST['val'])) ? $_REQUEST['val'] : "";
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
	function _details_section(){
		$ID = (isset($_REQUEST['val'])) ? $_REQUEST['val'] : "";
		$user = $this->f3->get("user");
		$userID = $user['ID'];


		$section = new models\sections();
		$section = $section->get($ID);

		$return = $section;

		$GLOBALS["output"]['data'] = $return;
	}


}
