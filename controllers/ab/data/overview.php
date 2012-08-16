<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace controllers\ab\data;
use \F3 as F3;
use \timer as timer;
use \models\ab as models;
use \models\user as user;


class overview extends data {
	function __construct() {

		$user = F3::get("user");
		$userID = $user['ID'];
		if (!$userID) exit(json_encode(array("error" => F3::get("system")->error("U01"))));

	}


	function _pages() {

		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];

		$settings = models\settings::_read("overview");

		$defaults = F3::get("defaults");

		$currentDate = $user['publication']['current_date'];
		$dID = $currentDate['ID'];
		$bookingsRaw = models\bookings::getAll("(ab_bookings.pID = '$pID' AND ab_bookings.dID='$dID') AND checked = '1' AND ab_bookings.deleted is null AND checked = '1' AND typeID='1' ", "client ASC");
		$stats = $this->_stats();

		$highlight = isset($_GET['highlight'])? $_GET['highlight']: $settings['highlight'];
		if (!$highlight){
			$highlight = $defaults['overview']['heighlight'];
		}

		$editionPages = $stats['loading']['pages'];


		$values = array();
		$values["overview"] = array(
			"highlight"    => $highlight,
		);


		models\user_settings::save_setting($values);
		//test_array($usersettings);



		$statsBlank = array(
			"records"=> array(
				"total"            => 0,
				"material"         => array(
					"y"=> 0,
					"n"=> 0
				),
				"material_approved"=> array(
					"y"=> 0,
					"n"=> 0
				)
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




		$bookings = array();
		foreach ($bookingsRaw as $booking){
			if ($booking['pageID']) {
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

				$bookings[$booking['pageID']][] = $a;
			}
		}



		$pagesReal = models\pages::getAll("global_pages.pID='$pID' AND global_pages.dID = '$dID'","page ASC");



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


			}
			$stats['records']['total']=count($records);
			$h = "";
			switch ($highlight){
				case "material":
					if ($stats['records']['material']['n']) $h = "no";
					if (!$stats['records']['material']['n'] && $stats['records']['material']['y']) $h = "yes";
					break;
				case "material_approved":
					if ($stats['records']['material_approved']['n']) $h = "no";
					if (!$stats['records']['material_approved']['n'] && $stats['records']['material_approved']['y']) $h = "yes";
					break;
				case "locked":
					if ($page['locked']=='1') $h = "no";
					break;
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
				"colour" => $page['colour'],
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
				if (isset($spread[$i])) {
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
				}
			}


		}

		if (count(($spread))) $spread[count($spread) - 1]['pages'][0]['side'] = "right";
		$a = array();
		foreach ($spread as $b) $a[] = $b;
		$spread = $a;

		$pages = array();
		$pages["spreads"] = $spread;
		$pages["count"] = $pagesCount;


		$return = $pages;
		$return['date'] = $currentDate['publish_date_display'];
		$return['dID'] = $currentDate['ID'];
		$return['stats'] = $stats;

		return $GLOBALS["output"]['data'] = $return;
	}


	function _page($page=""){
		$user = F3::get("user");
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
		$user = F3::get("user");
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
			$stats = models\record_stats::stats($statsData,array("cm","placed","placed_cm"), $loading['pages']);
			$stats['loading'] = $loading;

		} else {
			$stats = models\record_stats::stats($data,array("cm"));
			$loading = models\loading::getLoading($pID, $stats['cm'], $currentDate['pages']);
			$stats = models\record_stats::stats($data,array("cm","placed","placed_cm"), $loading['pages']);
			$stats['loading'] = $loading;
		}




		return $GLOBALS["output"]['data'] = $stats;

	}

	function _details_page(){
		$page_nr = (isset($_REQUEST['val'])) ? $_REQUEST['val'] : "";
		$user = F3::get("user");
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
		$user = F3::get("user");
		$userID = $user['ID'];


		$section = new models\sections();
		$section = $section->get($ID);

		$return = $section;

		$GLOBALS["output"]['data'] = $return;
	}


}
