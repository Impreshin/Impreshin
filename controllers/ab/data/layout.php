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
	private static function stats($dID, $placeID) {
		$user = \F3::get("user");
		$userID = $user['ID'];
		$pID = $user['ab_pID'];


	}
	function _list () {
		$user = \F3::get("user");
		$userID = $user['ID'];
		$pID = $user['ab_pID'];
		$currentDate = models\dates::getCurrent($pID);
		$dID = $currentDate['ID'];

		$placingID = (isset($_REQUEST['placingID']) && $_REQUEST['placingID'] != "") ? $_REQUEST['placingID'] : "";


		$records = models\bookings::getAll("(ab_bookings.pID = '$pID' AND dID='$dID') AND checked = '1' AND ab_bookings.deleted is null AND placingID='$placingID'");
		$records = models\bookings::display($records);
		if (count($records)) $records = $records[0]['records'];
		$return = array();
		$return['placing'] = F3::get("DB")->exec("SELECT ID, placing, (SELECT count(ID) FROM ab_bookings WHERE placingID =ab_placing.ID AND pID = '$pID' AND dID = '$dID' AND deleted is null AND checked = '1') AS recordCount FROM ab_placing WHERE pID = '$pID' ORDER BY orderby");
		$return['records'] = $records;
		$return['placingID'] = $placingID;
		return $GLOBALS["output"]['data'] = $return;
	}
	function _pages(){
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['ab_pID'];

		$currentDate = models\dates::getCurrent($pID);
		$dID = $currentDate['ID'];


		$sections = array(
			"Red"  => array(
				"n"=> "Red Section",
				"c"=> "red"
			),
			"green"=> array(
				"n"=> "Green Section",
				"c"=> "green"
			),
			"blue" => array(
				"n"=> "Blue Section",
				"c"=> "blue"
			),
			"1"     => array(
				"n"=> "",
				"c"=> ""
			),
			"2"     => array(
				"n"=> "",
				"c"=> ""
			),
			"3"     => array(
				"n"=> "",
				"c"=> ""
			),
			"4"     => array(
				"n"=> "",
				"c"=> ""
			),
			"5"     => array(
				"n"=> "",
				"c"=> ""
			)
		);
		$colours = array(
			"Full",
			"Spot",
			"none",
			""
		);
		$pagesArray = F3::get("DB")->exec("SELECT pages FROM ab_page_load WHERE pID = '$pID'");
		$d = array();
		foreach ($pagesArray as $p) $d[] = $p['pages'];


		$amount = $d[array_rand($d, 1)];


		$pages = array();
		for ($i = 0; $i < $amount; $i++) {
			$percent = rand(0, 100);
			$cm = (39 * 8);
			$cm = number_format($cm * ($percent / 100), 0);
			$pages[] = array(
				"page"   => $i + 1,
				"section"=> $sections[array_rand($sections, 1)],
				"colour" => $colours[array_rand($colours, 1)],
				"percent"=> $percent,
				"cm"     => $cm
			);

		}

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

		return $GLOBALS["output"]['data'] = $return;
	}


}
