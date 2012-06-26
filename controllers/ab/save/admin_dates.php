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


class admin_dates extends save {

	function _save() {
		$user = F3::get("user");
		$pID = $user['publication']['ID'];


		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";
		$publish_date = isset($_POST['publish_date']) ? $_POST['publish_date'] : "";
		$current = isset($_POST['current']) ? $_POST['current'] : "";


		$submit = true;


		$return = array(
			"error"   =>array(),
			"ID"=>$ID

		);
		if ($publish_date){
			$publish_date = date("Y-m-d",strtotime($publish_date));
			$exists = models\dates::getAll("global_dates.publish_date='$publish_date' AND global_dates.ID <> '$ID' AND pID='$pID'");


			if (count($exists)){
				$submit = false;
				$return['error'][] ="The date already exists";
			}
		}
		$values = array(
			"pID"         => $pID,
			"publish_date"=> $publish_date,
			"current"     => $current,
		);

		if ($submit){
			$ID = models\dates::save($ID, $values);
			$return['ID'] = $ID;
		}



	//	test_array(array("ID"=>$ID,"values"=>$values,"result"=>$return));


		return $GLOBALS["output"]['data'] = $return;

	}
	function _delete(){
		$user = F3::get("user");
		$pID = $user['publication']['ID'];
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		models\dates::_delete($ID);

	}

}