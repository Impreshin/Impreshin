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


class admin_placing extends save {
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

		$placing = isset($_POST['placing']) ? $_POST['placing'] : "";
		$rate = isset($_POST['rate']) ? $_POST['rate'] : "";




		$return = array(
			"error"   => array(),
			"ID"      => $ID

		);


		//test_array($p);
		$submit = true;



		if ($placing==""){
			$submit = false;
			$return['error'][] = "Need to specify a Placing";
		}

		if ($rate==""){
			$submit = false;
			$return['error'][] = "Need to specify a Rate";
		}









		$values = array(
			"placing"         => $placing,
			"pID"         => $pID,
			"rate"     => $rate,
		);





//$values = $values['p']['p'];


		if ($submit){
			$passed_ID = $ID;
			$ID = models\placing::save($ID, $values);

			$return['ID'] = $ID;
		}


	//	test_array(array("ID"=>$ID,"values"=>$values,"result"=>$return));


		return $GLOBALS["output"]['data'] = $return;

	}



	function _delete(){
		$user = F3::get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";
		models\placing::_delete($ID);
		return $GLOBALS["output"]['data'] = "done";

	}

	function _sort() {
		$user = F3::get("user");
		$cID = $user['publication']['cID'];
		$pID = $user['publication']['ID'];
		$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : "";
		$order = explode(",", $order);


		$i = 0;
		foreach ($order as $id) {
			F3::get("DB")->exec("UPDATE ab_placing SET orderby = '$i' WHERE ID = '$id' AND pID = '$pID'");
			$i++;
		}


		return $GLOBALS["output"]['data'] = "done";

	}


}
