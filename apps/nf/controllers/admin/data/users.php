<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\nf\controllers\admin\data;

use \timer as timer;
use \apps\nf\models as models;
use \models\user as user;


class users extends \apps\nf\controllers\data\data {
	function __construct() {
		parent::__construct();

	}
	function _list() {
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];

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

		models\settings::save($values);





		$records = user::getAll("cID='$cID'", $ordering_c . " " . $ordering_d . ", fullName ASC");
//test_array($records); 
		$apps = $this->f3->get("CFG");
		$apps = $apps['apps'];

		$apps_str = "";


		$a = array();
		foreach ($records as $record){

				$t = time() - strtotime($record['last_activity']);
				if ($t < 172800 * 1){
					$t = 1;
				} elseif ($t < 172800 * 3){
					$t = 2;
				} else {
					$t = 3;
				}

				$record['last_activity'] = array(
					"time"=> $record['last_activity'],
					"display"=>timesince($record['last_activity']),
					"active"=>$t
				);


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
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];

		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";





		$details = new user();
		$details = $details->get($ID);

		$return = array();
		$publications = \models\publications::getAll("cID='$cID'", "publication ASC");

		if (!$details['ID']){
			$userPublications = array();
		} else {
			$userPublications = \models\publications::getAll_user("nf_users_pub.uID='" . $details['ID'] . "'", "publication ASC");
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


		$extra = $this->f3->get("DB")->exec("SELECT * FROM global_users_company WHERE uID='" . $details['ID'] . "' AND cID='" . $cID . "'");
		$permissions = \apps\nf\permissions::defaults($user['company']['ID']);
		$permissions = $permissions["p"];


		
		if (count($extra)) {
			$extra = $extra[0];
			if ($extra['nf_permissions']){
				$user_permissions = @unserialize($extra['nf_permissions']);
				$permissions = array_replace_recursive((array)$permissions, (array)($user_permissions) ? $user_permissions : array());
				
			}

		} 
		


		

		

		

		

		//test_array($stage_permissions); 
		
		





		if (count($extra)) {
			$return['details']['nf'] = $extra['nf'];
			$return['details']['permissions'] = $permissions;
			$return['details']['allow_setup'] = $extra['allow_setup'];
			$return['details']['nf_author'] = $extra['nf_author'];
		} else {
			$return['details']['nf'] = '1';

		}




		$GLOBALS["output"]['data'] = $return;
	}


}
