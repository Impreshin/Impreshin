<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace controllers\ab;
use \F3 as F3;
use \timer as timer;
use \models\ab\dates as dates;
use \models\ab\bookings as bookings;
use \models\ab\production as production;
class controller_layout {
	function __construct() {
		$user = F3::get("user");
		$userID = $user['ID'];
		//if (!$userID) F3::reroute("/login");
		\models\user::save_config(array("page"=> $_SERVER['REQUEST_URI']));
	}
	function page() {
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['ab_pID'];

		$currentDate = dates::getCurrent($pID);
		$dID = $currentDate['ID'];


		$sections = array(
			"Red"=>array(
				"n"=>"Red Section",
				"c"=>"red"
			),
			"green"=> array(
				"n"=> "Green Section",
				"c"=> "green"
			),
			"blue"=> array(
				"n"=> "Blue Section",
				"c"=> "blue"
			),
			""=> array(
				"n"=> "",
				"c"=> ""
			)
		);
		$colours = array("Full","Spot","none","");



		$pages = array();
		for ($i = 0; $i < 8; $i++) {
			$percent = rand(0, 100);
			$cm = (39 * 8);
			$cm = number_format($cm * ($percent / 100),0);
			$pages[] = array(
				"page"   => $i + 1,
				"section"=> $sections[array_rand($sections, 1)],
				"colour" => $colours[array_rand($colours, 1)],
				"percent"=> $percent,
				"cm"=>$cm
			);

		}

		$pagesCount = count($pages);
		$spreads = ($pagesCount / 2)+1;



		$t = array(
			"count"=>$pagesCount,
			"spreads"=>$spreads
		);
		//test_array($t);

		$spread = array();

		$h = 0;
		for ($i = 0; $i < $spreads; $i++) {
			$page = $pages[$h++];
			$page['side']="left";
			$spread[$i]['pages'][]= $page;
			if ($i>0){
				if (isset($pages[$h])){
					$page = $pages[$h++];
					$page['side'] = "right";

					$spread[$i]['pages'][] = $page;
				}
			}
			$spread[$i]['side'] = (strpos($i / 2, ".")) ? "right" : "left";
			$spread[$i]['index']=$i;


		}

$spread[count($spread)-1]['pages'][0]['side']="right";
$pages=$spread;




//test_array($spread);
		$records = bookings::getAll("(ab_bookings.pID = '$pID' AND dID='$dID') AND checked = '1' AND ab_bookings.deleted is null AND placingID='1'");


		$records = bookings::display($records);
		//test_array($records);



		//test_array($user);
		$ab_settings = F3::get("ab_settings");
		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","ui/adbooker/");
		$tmpl->page = array(
			"section"=> "layout",
			"sub_section"=> "view",
			"template"=> "page_layout",
			"meta"    => array(
				"title"=> "AdBooker - Layout",
			)
		);
		$tmpl->pages = $pages;
		$tmpl->records = $records;
		$tmpl->output();

	}

}
