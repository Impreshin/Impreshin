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


class admin_marketers extends save {
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

		$marketer = isset($_POST['marketer']) ? $_POST['marketer'] : "";
		$number = isset($_POST['number']) ? $_POST['number'] : "";
		$email = isset($_POST['email']) ? $_POST['email'] : "";
		$uID = isset($_POST['uID']) ? $_POST['uID'] : "";
		$publications = isset($_POST['publications']) ? $_POST['publications'] : array();


		$return = array(
			"error"   => array(),
			"ID"      => $ID

		);


		//test_array($p);
		$submit = true;



		if ($marketer==""){
			$submit = false;
			$return['error'][] = "Need to specify a Marketer Name";
		}









		$values = array(
			"marketer"         => $marketer,
			"number"         => $number,
			"email"         => $email,
			"uID"         => $uID,
			"publications"     => $publications,
			"cID"=> $cID
		);





//$values = $values['p']['p'];


		if ($submit){
			$passed_ID = $ID;
			$ID = models\marketers::save($ID, $values);

			$return['ID'] = $ID;
		}


	//	test_array(array("ID"=>$ID,"values"=>$values,"result"=>$return));


		return $GLOBALS["output"]['data'] = $return;

	}



	function _delete(){
		$user = F3::get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";
		models\marketers::_delete($ID);
		return $GLOBALS["output"]['data'] = "done";

	}



}
