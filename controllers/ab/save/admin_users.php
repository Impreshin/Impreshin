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


class admin_users extends save {

	function _save() {
		$user = F3::get("user");
		$pID = $user['publication']['ID'];
		$cID = $user['publication']['cID'];


		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$fullName = isset($_POST['fullName']) ? $_POST['fullName'] : "";
		$email = isset($_POST['email']) ? $_POST['email'] : "";
		$password= isset($_POST['password']) ? $_POST['password'] : "";
		$publications= isset($_POST['publications']) ? $_POST['publications'] : "";
		$permissions= isset($_POST['permissions']) ? $_POST['permissions'] : "";


		$publications = models\publications::getAll("cID='$cID'", "publication ASC");

		$pstr = array();
		foreach ($publications as $u) $pstr[] = $u['ID'];






		//test_array($p);
		$submit = true;


		$return = array(
			"error"   =>array(),
			"ID"=>$ID

		);

		$values = array(
			"fullName"         => $fullName,
			"email"=> $email,
			"publications"     => $publications,
			"available_publications"     => $pstr,
			"cID"=> $cID
		);


		if ($password)$values['password'] = $password;



//$values = $values['p']['p'];




		if ($submit){
			$ID = user::save($ID, $values);

			// save to company here


			models\user_permissions::write($ID, $cID, $permissions);
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
