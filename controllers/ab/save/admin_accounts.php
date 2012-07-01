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


class admin_accounts extends save {
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

		$account = isset($_POST['account']) ? $_POST['account'] : "";
		$accNum = isset($_POST['accNum']) ? $_POST['accNum'] : "";
		$statusID= isset($_POST['statusID']) ? $_POST['statusID'] : "";
		$remark = isset($_POST['remark']) ? $_POST['remark'] : "";
		$publications= isset($_POST['publications']) ? $_POST['publications'] : array();


		$return = array(
			"error"   => array(),
			"ID"      => $ID

		);


		//test_array($p);
		$submit = true;



		if ($account==""){
			$submit = false;
			$return['error'][] = "Need to specify an Account";
		}
		if ($accNum==""){
			$submit = false;
			$return['error'][] = "Need to specify an Account Number";
		}

		$accs = models\accounts::getAll("accNum='$accNum' AND ab_accounts.cID = '$cID' AND ab_accounts.ID <> '$ID'");


			if (count($accs)) {
				$submit = false;
				$return['error'][] = "An account with that account number already exists";
			}






		$values = array(
			"account"         => $account,
			"accNum"=> $accNum,
			"statusID"=> $statusID,
			"remark"=> $remark,
			"publications"     => $publications,
			"cID"=> $cID
		);





//$values = $values['p']['p'];


		if ($submit){
			$passed_ID = $ID;
			$ID = models\accounts::save($ID, $values);

			$return['ID'] = $ID;
		}


	//	test_array(array("ID"=>$ID,"values"=>$values,"result"=>$return));


		return $GLOBALS["output"]['data'] = $return;

	}
	function add_company(){
		$user = F3::get("user");
		$cID = $user['publication']['cID'];

		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";


		user::_add_company($ID, $cID);
		return $GLOBALS["output"]['data'] = array("ID"=>$ID);
	}
	function add_app(){
		$user = F3::get("user");
		$cID = $user['publication']['cID'];

		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$app = F3::get("app");

		user::_add_app($ID, $cID, $app);
		return $GLOBALS["output"]['data'] = array("ID"=>$ID);
	}
	function remove_app(){
		$user = F3::get("user");
		$cID = $user['publication']['cID'];

		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$app = F3::get("app");

		user::_remove_app($ID, $cID, $app);
		return $GLOBALS["output"]['data'] = array("ID"=>$ID);
	}
	function _delete(){
		$user = F3::get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";



	}
	function _pub(){
		$user = F3::get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$pID = $user['publication']['ID'];


		$p = new Axon("ab_accounts_pub");
		$p->load("aID='$ID' and pID='$pID'");
		if (!$p->ID){
			$p->aID=$ID;
			$p->pID=$pID;
			$p->save();
		} else {
			$p->erase();

		}


		return  "done";
	}

}
