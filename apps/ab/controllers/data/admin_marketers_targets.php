<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\ab\controllers\data;

use \timer as timer;
use \apps\ab\models as models;
use \models\user as user;


class admin_marketers_targets extends data {
	function __construct() {
		parent::__construct();

	}

	function _list() {

		$user = $this->f3->get("user");
		$userID = $user['ID'];

		$pID = $user['publication']['ID'];
		$cID = $user['publication']['cID'];

		$mID = (isset($_REQUEST['mID'])) ? $_REQUEST['mID'] : "";

		$selectedpage = (isset($_REQUEST['page'])) ? $_REQUEST['page'] : "";
		$nrrecords = (isset($_REQUEST['nr'])) ? $_REQUEST['nr'] : 10;


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
			"marketerID" => $mID,
			"order" => array(
				"c" => $ordering_c,
				"o" => $ordering_d
			),

		);




		models\settings::save($values);





		$where = "mID = '$mID' and date_to and date_from";

		$recordsFound = models\marketers_targets::getAll_count($where);
		$limit = $nrrecords;
		$pagination = new \pagination();
		$pagination = $pagination->calculate_pages($recordsFound, $limit, $selectedpage, 7);





		$records = models\marketers_targets::getAll($where, $ordering_c . " " . $ordering_d . ",date_to DESC", $pagination['limit']);
		//$records = \models\dates::getAll("pID='$pID'", $ordering_c . " " . $ordering_d . ",publish_date DESC", $pagination['limit']);
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
		$return['pagination'] = $pagination;
		$return['records'] = $a;

		return $GLOBALS["output"]['data'] = $return;
	}

	function _details() {
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['publication']['ID'];
		$cID = $user['publication']['cID'];

		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";

		$o = new models\marketers_targets();
		$details = $o->get($ID);

		$ID = $details['ID'];





		$return = array();
		$publications = \models\publications::getAll("cID='$cID'", "publication ASC");

		if (!$details['ID']) {
			$userPublications = array();
		} else {
			$userPublications = $this->f3->get("DB")->exec("SELECT pID FROM ab_marketers_targets_pub WHERE mtID = '$ID'");
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
