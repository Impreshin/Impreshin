<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\ab\controllers\data;

use \timer as timer;
use \apps\ab\models as models;
use \models\user as user;


class admin_production extends data {
	function __construct() {
		parent::__construct();

	}

	function _list() {
		$user = $this->f3->get("user");
		$userID = $user['ID'];

		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];


		$section = "admin_production";

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
		models\settings::save($values);


		$where = "cID='$cID'";



		$records = models\production::getAll($where, $ordering_c . " " . $ordering_d . ", production ASC");

		$return = array();

		$return['records'] = $records;

		return $GLOBALS["output"]['data'] = $return;
	}

	function _details() {
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];

		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";

		$o = new models\production();
		$details = $o->get($ID);

		$ID = $details['ID'];


		$return = array();


		$return = array();
		$publications = \models\publications::getAll("cID='$cID'", "publication ASC");

		if (!$details['ID']) {
			$userPublications = array();
		} else {
			$userPublications = $this->f3->get("DB")->exec("SELECT pID FROM ab_production_pub WHERE productionID = '$ID'");
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

		if ($details['ID']) {
			$where = "material_productionID='" . $details['ID'] . "' AND ab_bookings.pID in (" . implode(",", $pIDarray) . ")";
			$recordsFound = models\bookings::getAll_count($where);
		} else {
			$recordsFound = 0;
		}
		$return['records'] = $recordsFound;


		return $GLOBALS["output"]['data'] = $return;
	}

}
