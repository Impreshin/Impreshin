<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace controllers\setup\data\ab;

use \timer as timer;
use \apps\ab\models as models;
use \models\user as user;


class accounts {
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

		$records = models\accounts::getAll($where, "account ASC", $pagination['limit'],$pID);


		$return = array();
		$return['pagination'] = $pagination;
		$return['records'] = $records;
		$return['count_pub'] = count(models\accounts::getAll("ab_accounts_pub.pID = '".$pID."'", "account ASC", "", $pID));;

		return $GLOBALS["output"]['data'] = $return;
	}


	function _details() {
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $_GET['pID'];
		$cID = $_GET['cID'];

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
		$publications = \models\publications::getAll("cID='$cID'", "publication ASC");

		if (!$details['ID']) {
			$userPublications = array();
		} else {
			$userPublications = $this->f3->get("DB")->exec("SELECT pID FROM ab_accounts_pub WHERE aID = '$ID'");
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
		$return['status'] = models\accountStatus::getAll("cID='$cID'", "orderby ASC");

		if ($details['ID']){
			$where = "ab_accounts.ID='" . $details['ID'] . "'";
			$recordsFound = models\bookings::getAll_count($where);
		} else {
			$recordsFound = 0;
		}




		$return['records'] = $recordsFound;

		return $GLOBALS["output"]['data'] = $return;
	}


}
