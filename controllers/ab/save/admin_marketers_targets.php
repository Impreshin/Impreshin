<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace controllers\ab\save;
use \F3 as F3;
use \Axon as Axon;
use \timer as timer;
use \models\ab as models;
use \models\user as user;


class admin_marketers_targets extends save {
	function __construct() {

		$user = F3::get("user");
		$userID = $user['ID'];
		if (!$userID) exit(json_encode(array("error" => F3::get("system")->error("U01"))));

	}

	function _save() {
		$user = F3::get("user");
		$pID = $user['publication']['ID'];
		$cID = $user['publication']['cID'];


		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";




		$return = array(
			"error"   => array(),
			"ID"      => $ID

		);


		//test_array($p);
		$submit = true;

		$post = $_POST;

		$a = array();
		foreach ($post as $key => $value){
			$d = str_replace("target","01", $key);

			$m = date("m",strtotime($d));
			$y = date("Y",strtotime($d));

			$s = array(
				"pID"=>$pID,
				"mID"=>$ID,
				"monthin"=>$m,
				"yearin"=>$y,
				"target"=>$value
			);


			$a[] = $s;
		}



		$axon = new Axon("ab_marketers_targets");
		foreach ($a as $record){
			$axon->load("mID='".$record['mID']."' AND pID ='".$record['pID']."' AND monthin ='" . $record['monthin'] ."' AND yearin = '" . $record['yearin'] ."'");
			foreach ($record as $key => $value){
				$axon->$key = $value;
			}
			if ($record['target']){
				$axon->save();
				$axon->reset();
			} else {
				if (!$axon->dry()){
					$axon->erase();
				}
			}

		}










		return $GLOBALS["output"]['data'] = $return;

	}



	function _delete(){
		$user = F3::get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";
		models\marketers::_delete($ID);
		return $GLOBALS["output"]['data'] = "done";

	}

	function _pub() {
		$user = F3::get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$pID = $user['publication']['ID'];


		$p = new Axon("ab_marketers_pub");
		$p->load("mID='$ID' and pID='$pID'");
		if (!$p->ID) {
			$p->mID = $ID;
			$p->pID = $pID;
			$p->save();
		} else {
			$p->erase();

		}


		return "done";
	}


}
