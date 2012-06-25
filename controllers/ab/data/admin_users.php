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


class admin_users extends data {
	function _list() {
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];
		$cID = $user['publication']['cID'];

		$records = user::getAll("cID='$cID'","fullName ASC");

		$apps = F3::get("cfg");
		$apps = $apps['apps'];

		$apps_str = "";


		$a = array();
		foreach ($records as $record){
			foreach ($apps as $app) {

				$record[$app.'_last_activity'] = array(
					"time"=> $record[$app . '_last_activity'],
					"display"=>timesince($record[$app . '_last_activity']),
					"active"=>((time() - strtotime($record[$app . '_last_activity']))<(172800*3))?1:0
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
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['publication']['ID'];
		$cID = $user['publication']['cID'];

		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";



		$details = new user();
		$details = $details->get($ID);

		$return = array();
		$publications = models\publications::getAll("cID='$cID'", "publication ASC");

		$pstr = array();
		foreach ($details['publications'] as $u) $pstr[] = $u['ID'];

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
		$return['permissions'] = $publications;



		$GLOBALS["output"]['data'] = $return;
	}

}
