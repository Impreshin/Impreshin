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


class admin_placing_colours extends data {
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

		$placingID = (isset($_REQUEST['placingID']))?$_REQUEST['placingID']:"";



		$where = "placingID='$placingID'";



		$records = models\colours::getAll($where, "orderby ASC");

		$return = array();

		$placings = models\placing::getAll("pID='$pID'", "orderby ASC");

		$return['records'] = $records;
		$return['placing'] = $placings;
		return $GLOBALS["output"]['data'] = $return;
	}

	function _details() {
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['publication']['ID'];
		$cID = $user['publication']['cID'];

		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";

		$o = new models\colours();
		$details = $o->get($ID);

		$ID = $details['ID'];


		$return = array();


		$return = array();





		$return['details'] = $details;


		if ($details['ID']) {
			$where = "colourID='" . $details['ID'] . "'";
			$recordsFound = models\bookings::getAll_count($where);
		} else {
			$recordsFound = 0;
		}
		$return['records'] = $recordsFound;


		return $GLOBALS["output"]['data'] = $return;
	}

}
