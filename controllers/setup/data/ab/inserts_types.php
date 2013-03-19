<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace controllers\setup\data\ab;
use \F3 as F3;
use \timer as timer;
use \models\ab as models;
use \models\user as user;


class inserts_types {
	function __construct() {
		$this->f3 = \base::instance();
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		if (!$userID) exit(json_encode(array("error" => $this->f3->get("system")->error("U01"))));

	}

	function _list() {

		$user = $this->f3->get("user");
		$userID = $user['ID'];

		$pID = $_GET['pID'];
		$cID = $_GET['cID'];





		$where = "pID='$pID'";



		$records = models\inserts_types::getAll($where, "orderby ASC");

		$return = array();

		$return['records'] = $records;

		return $GLOBALS["output"]['data'] = $return;
	}

	function _details() {

		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $_GET['pID'];
		$cID = $_GET['cID'];
		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";

		$o = new models\inserts_types();
		$details = $o->get($ID);

		$ID = $details['ID'];


		$return = array();


		$return = array();

		$return['details'] = $details;

		if ($details['ID']) {
			$where = "ab_bookings.insertTypeID='" . $details['ID'] . "'";
			$recordsFound = models\bookings::getAll_count($where);
		} else {
			$recordsFound = 0;
		}
		$return['records'] = $recordsFound;


		return $GLOBALS["output"]['data'] = $return;
	}

}