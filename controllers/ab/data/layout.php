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


class layout extends data {

	function _list() {
		$user = \F3::get("user");

		$userID = $user['ID'];
		$pID = $user['ab_pID'];
		$currentDate = $user['ab_publication']['current_date'];
		$dID = $currentDate['ID'];

		$placingID = (isset($_REQUEST['placingID']) && $_REQUEST['placingID'] != "") ? $_REQUEST['placingID'] : "";

		$stats = $this->_stats();

		$maxPage = $stats['loading']['pages'];




		$records = models\bookings::getAll("(ab_bookings.pID = '$pID' AND ab_bookings.dID='$dID') AND checked = '1' AND ab_bookings.deleted is null AND placingID='$placingID' AND (page is null OR page > '$maxPage')");
		$rawBookings= $records;
		$records = models\bookings::display($records);
		if (count($records)) $records = $records[0]['records'];
		$return = array();
		$return['placing'] = F3::get("DB")->exec("SELECT ID, placing, (SELECT count(ab_bookings.ID) FROM ab_bookings LEFT JOIN global_pages ON ab_bookings.pageID = global_pages.ID WHERE placingID =ab_placing.ID AND ab_bookings.pID = '$pID' AND ab_bookings.dID = '$dID' AND deleted is null AND checked = '1' AND (page is null OR page > '$maxPage')) AS recordCount FROM ab_placing WHERE pID = '$pID' ORDER BY orderby");

		$cols = array(
			"ID",
			"client",
			"colour",
			"colourSpot",
			"colourLabel",
			"col",
			"cm",
			"totalspace",
			"category",
			"remark",
			"remarkTypeID",
			"userName",
			"checked_user",
			"material_status",
			"material_approved",
			"remarkType",
			"remarkTypeLabelClass",
		);


		$r = array();
		foreach ($records as $record){
			$b = array();
			foreach ($cols as $col){
				$b[$col] = $record[$col];
			}
			$r[] = $b;
		}
		$records = $r;

		$return['records'] = $records;
		$return['placingID'] = $placingID;
		$return['date'] = $currentDate['publish_date_display'];
		$return['dID'] = $currentDate['ID'];
		$return['stats'] = $stats;


		return $GLOBALS["output"]['data'] = $return;
	}

	function _pages() {

		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['ab_pID'];

		$currentDate = $user['ab_publication']['current_date'];
		$dID = $currentDate['ID'];
		$bookingsRaw = models\bookings::getAll("(ab_bookings.pID = '$pID' AND ab_bookings.dID='$dID') AND checked = '1' AND ab_bookings.deleted is null AND checked = '1' ", "client ASC");
		$stats = $this->_stats($bookingsRaw);


		$editionPages = $stats['loading']['pages'];


		$blank = array(
			"page"   => 0,
			"section"=> array(
				"n"=> "",
				"c"=> "",

			),
			"colour" => ""
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

				$bookings[$booking['pageID']][] = $a;
			}
		}



		$pagesReal = models\pages::getAll("global_pages.pID='$pID' AND global_pages.dID = '$dID'","page ASC");

		$r = array();
		foreach ($pagesReal as $page){
			$r[$page['page']] = array(
				"page"   => $page['page'],
				"section"=> array(
					"i"=>$page['sectionID'],
					"n"=>$page['section'],
					"c"=>$page['section_colour']
				),
				"colour" => $page['colour'],
				"percent"=> $page['percent'],
				"cm"     => $page['cm'],
				"records"=>isset($bookings[$page['ID']])?$bookings[$page['ID']]:array()
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
		//test_array($t);

		$spread = array();

		$h = 0;
		for ($i = 0; $i < $spreads; $i++) {
			$page = $pages[$h++];
			$page['side'] = "left";
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

		$spread[count($spread) - 1]['pages'][0]['side'] = "right";
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


	function _page($page){
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['ab_pID'];

		$page = ($page)?$page: isset($_REQUEST['page'])?$_REQUEST['page']:"";

		$currentDate = $user['ab_publication']['current_date'];
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
		$bookingsRaw = models\bookings::getAll("(ab_bookings.pID = '$pID' AND ab_bookings.dID='$dID') AND checked = '1' AND ab_bookings.deleted is null AND checked = '1' AND pageID = '$pageID' ", "client ASC");
		$bookings = array();
		foreach ($bookingsRaw as $booking) {
			if ($booking['pageID']) {
				$a = array();
				$a['ID'] = $booking['ID'];
				$a['client'] = $booking['client'];
				$a['colour'] = $booking['colour'];
				$a['colourSpot'] = $booking['colourSpot'];
				$a['col'] = $booking['col'];
				$a['cm'] = $booking['cm'];
				$a['totalspace'] = $booking['totalspace'];

				$bookings[] = $a;
			}
		}

		$r['records'] = $bookings;


		$return = $r;

		return $GLOBALS["output"]['data'] = $return;
	}
	function _stats($data="") {
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['ab_pID'];

		$currentDate = $user['ab_publication']['current_date'];
		$dID = $currentDate['ID'];


		$pages = models\pages::maxPages($currentDate, "(ab_bookings.pID = '$pID' AND ab_bookings.dID='$dID') AND ab_bookings.deleted is null AND 1");



		$where = "(ab_bookings.pID = '$pID' AND ab_bookings.dID='$dID') AND ab_bookings.checked = '1' AND ab_bookings.deleted is null ";

		if (!is_array($data)) $data = $where;
		$stats = models\record_stats::stats_layout($data);
		$stats['loading'] = models\loading::getLoading($pID, $stats['cm'], $pages);


		return $GLOBALS["output"]['data'] = $stats;

	}

	function _details_page(){
		$page_nr = (isset($_REQUEST['val'])) ? $_REQUEST['val'] : "";
		$user = F3::get("user");
		$userID = $user['ID'];

		$pID = $user['ab_publication']['ID'];

		$dID = $user['ab_publication']['current_date']['ID'];

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
