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


class admin_marketers_targets extends data {
	function __construct() {

		$user = F3::get("user");
		$userID = $user['ID'];
		if (!$userID) exit(json_encode(array("error" => F3::get("system")->error("U01"))));

	}

	function _list() {
		$user = F3::get("user");
		$userID = $user['ID'];

		$pID = $user['publication']['ID'];
		$cID = $user['publication']['cID'];


		$section = "admin_marketers_targets";

		$settings = models\settings::_read($section);

		$ordering_c = (isset($_REQUEST['order']) && $_REQUEST['order'] != "") ? $_REQUEST['order'] : $settings['order']['c'];
		$ordering_d = $settings['order']['o'];

		if ((isset($_REQUEST['order']) && $_REQUEST['order'] != "")) {
			if ($settings['order']['c'] == $_REQUEST['order']) {
				if ($ordering_d == "ASC") {
					$ordering_d = "DESC";
				} else {
					$ordering_d = "ASC";
				}

			}

		}


		$values = array();
		$values[$section] = array(
			"order"      => array(
				"c"=> $ordering_c,
				"o"=> $ordering_d
			),

		);

		models\user_settings::save_setting($values);




		$where = "pID='$pID'";



		$records = models\marketers::getAll($where, $ordering_c . " " . $ordering_d . ", marketer ASC");

		$return = array();

		$return['records'] = $records;

		return $GLOBALS["output"]['data'] = $return;
	}

	function _details() {
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['publication']['ID'];
		$cID = $user['publication']['cID'];

		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";



		$data = models\marketers_targets::getAll("mID='$ID' AND pID = '$pID' ", "STR_TO_DATE(concat('01,',monthin,',',yearin),'%d,%m,%Y') DESC");


		$a = array();
		foreach ($data as $target){
			$month = date("mY", strtotime($target['m']));
			$a[$month] = $target['target'];

		}


		$dates = models\dates::getAll("pID='$pID' AND publish_date > DATE_ADD(now(), INTERVAL -5 MONTH)","publish_date DESC");


		//test_array($dates);
		$t = array();
		foreach($dates as $date){

			$month = date("mY",strtotime($date['publish_date']));
			$t[$month] = array(
				"month"=> date("m-Y", strtotime($date['publish_date'])),
				"label"=> date("F Y", strtotime($date['publish_date'])),
				"target"=>(isset($a[$month]))?$a[$month]:"",
				"current"=>(date("m-Y")== date("m-Y", strtotime($date['publish_date'])))?1:0
			);
		}
		$c = array();
		foreach ($t as $b){
			$c[] = $b;
		}


		$return = array();
		if ($ID){
			$return['details'] = $c;
		} else {
			$return['details'] = "";
		}








		return $GLOBALS["output"]['data'] = $return;
	}

}
