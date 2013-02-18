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


class users {
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

		$section = "admin_users";

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





		$records = user::getAll("cID='$cID'", $ordering_c . " " . $ordering_d . ", fullName ASC");

		$apps = $this->f3->get("cfg");
		$apps = $apps['apps'];

		$apps_str = "";


		$a = array();
		foreach ($records as $record){
			foreach ($apps as $app) {
				$t = time() - strtotime($record[$app . '_last_activity']);
				if ($t < 172800 * 1){
					$t = 1;
				} elseif ($t < 172800 * 3){
					$t = 2;
				} else {
					$t = 3;
				}

				$record[$app.'_last_activity'] = array(
					"time"=> $record[$app . '_last_activity'],
					"display"=>timesince($record[$app . '_last_activity']),
					"active"=>$t
				);

			}
			$a[] = $record;
		}
		$records = $a;


		$return = array();
		$return['records'] = $records;

		$GLOBALS["output"]['data'] = $return;
	}
	function _details(){
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $_GET['pID'];
		$cID = $_GET['cID'];

		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";





		$details = new user();
		$details = $details->get($ID);

		$return = array();
		$publications = \models\publications::getAll("cID='$cID'", "publication ASC");

		if (!$details['ID']){
			$userPublications = array();
		} else {
			$userPublications = \models\publications::getAll_user("ab_users_pub.uID='" . $details['ID'] . "'", "publication ASC");
		}


		$pstr = array();
		foreach ($userPublications as $u) $pstr[] = $u['ID'];

		$pubarray = array();
		foreach ($publications as $pub){
			$pub['selected'] = 0;
			if (in_array($pub['ID'], $pstr)){
				$pub['selected'] = 1;
			}

			$pubarray[] = $pub;
		}

		$publications = $pubarray;
		$return['details'] = $details;
		$return['publications'] = $publications;


		$extra = $this->f3->get("DB")->exec("SELECT * FROM global_users_company WHERE uID='" . $details['ID'] . "' AND cID='" . $user['publication']['cID'] . "'");

		if (count($extra)) {
			$extra = $extra[0];
			$return['details']['ab'] = $extra['ab'];
			$return['details']['permissions'] = models\user_permissions::_read($extra['ab_permissions']);
			$return['details']['ab_marketerID'] = $extra['ab_marketerID'];
			$return['details']['ab_productionID'] = $extra['ab_productionID'];
			$return['details']['allow_setup'] = $extra['allow_setup'];
		} else {
			$return['details']['ab'] = '1';

		}




		$GLOBALS["output"]['data'] = $return;
	}


}
