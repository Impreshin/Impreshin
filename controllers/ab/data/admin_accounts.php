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


class admin_accounts extends data {
	function __construct() {

		$user = F3::get("user");
		$userID = $user['ID'];
		if (!$userID) exit(json_encode(array("error" => F3::get("system")->error("U01"))));

	}

	function _list() {
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];

		$cID = $user['publication']['cID'];

		$section = "admin_accounts";

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


		$selectedpage = (isset($_REQUEST['page'])) ? $_REQUEST['page'] : "";
		$nrrecords = (isset($_REQUEST['nr'])) ? $_REQUEST['nr'] : 10;


		$search = (isset($_REQUEST['search'])) ? $_REQUEST['search'] : "";
		$statusID = (isset($_REQUEST['statusID'])) ? $_REQUEST['statusID'] : "";

		if ($statusID=='*'){
			$statusID = "";
		}



		$where = "ab_accounts.cID='$cID'";

		if ($search){
			$where .= " AND (account like '%$search%' OR accNum like '%$search%') ";
		}
		if ($statusID){
			$where .= " AND (statusID= '$statusID') ";
		}


		$recordsFound = models\accounts::getAll_count($where);
		$limit = $nrrecords;
		$pagination = new \pagination();
		$pagination = $pagination->calculate_pages($recordsFound, $limit, $selectedpage, 7);

		$records = models\accounts::getAll($where, $ordering_c." ". $ordering_d.", account ASC", $pagination['limit']);

		$return = array();
		$return['pagination'] = $pagination;
		$return['records'] = $records;

		return $GLOBALS["output"]['data'] = $return;
	}

	function _details() {
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['publication']['ID'];
		$cID = $user['publication']['cID'];

		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";

		$o = new models\accounts();
		$details = $o->get($ID);

		if ($details['statusID']==""){
			$accountStatus = models\accountStatus::getAll("cID='$cID'", "orderby ASC");
			if (isset($accountStatus[0]['ID'])){
				$details['statusID'] = $accountStatus[0]['ID'];
			}

		}

		$return = array();
		$publications = models\publications::getAll("cID='$cID'", "publication ASC");

		if (!$details['ID']) {
			$userPublications = array();
		} else {
			$userPublications = F3::get("DB")->exec("SELECT pID FROM ab_accounts_pub WHERE aID = '$ID'");
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

		if ($details['ID']){
			$where = "ab_accounts.ID='" . $details['ID'] . "' AND ab_bookings.pID in (" . implode(",", $pIDarray) . ")";
			$recordsFound = models\bookings::getAll_count($where);
		} else {
			$recordsFound = 0;
		}




		$return['records'] = $recordsFound;

		return $GLOBALS["output"]['data'] = $return;
	}

}
