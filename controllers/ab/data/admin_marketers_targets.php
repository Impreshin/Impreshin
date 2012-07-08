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

		$mID = (isset($_REQUEST['mID'])) ? $_REQUEST['mID'] : "";


		$section = "admin_marketers_targets";


		$values = array();
		$values[$section] = array(
			"marketerID"      => $mID

		);

		models\user_settings::save_setting($values);





		$where = "mID = '$mID' AND DATEDIFF(date_to,current_date()) > -30";



		$records = models\marketers_targets::getAll($where, " DATEDIFF(date_to,current_date()) ASC");
		$a = array();
		foreach ($records as $target){
			$target['target'] = currency($target['target']);
			$target['active'] = 0;

			$date_from = date("Y-m-d", strtotime($target['date_from']));
			$date_to = date("Y-m-d", strtotime($target['date_to']));
			$today = date("Y-m-d");

			if ($date_to >= $today && $date_from <= $today) $target['active'] = 1;

			$a[] = $target;
		}

		$return = array();

		$return['records'] = $a;

		return $GLOBALS["output"]['data'] = $return;
	}

	function _details() {
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['publication']['ID'];
		$cID = $user['publication']['cID'];

		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";

		$o = new models\marketers_targets();
		$details = $o->get($ID);

		$ID = $details['ID'];





		$return = array();
		$publications = models\publications::getAll("cID='$cID'", "publication ASC");

		if (!$details['ID']) {
			$userPublications = array();
		} else {
			$userPublications = F3::get("DB")->exec("SELECT pID FROM ab_marketers_targets_pub WHERE mtID = '$ID'");
		}


		$pstr = array();
		foreach ($userPublications as $u) $pstr[] = $u['pID'];

		$pubarray = array();
		$pIDarray = array();
		foreach ($publications as $pub) {
			$pub['selected'] = 0;
			if (in_array($pub['ID'], $pstr)) {
				$pub['selected'] = 1;
			}
			$pIDarray[] = $pub['ID'];
			$pubarray[] = $pub;
		}

		$publications = $pubarray;
		$return['details'] = $details;
		$return['publications'] = $publications;





		return $GLOBALS["output"]['data'] = $return;
	}

}
