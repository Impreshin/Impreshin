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
		$email = isset($_POST['email']) ? strtolower($_POST['email']) : "";
		$password= isset($_POST['password']) ? $_POST['password'] : "";
		$publications= isset($_POST['publications']) ? $_POST['publications'] : array();
		$permissions= isset($_POST['permissions']) ? $_POST['permissions'] : array();


		$return = array(
			"error"   => array(),
			"ID"      => $ID

		);


		//test_array($p);
		$submit = true;

		$email_check = user::check_email($email);
		if (isset($email_check['ID'])){
			if ($email_check['ID']!=$ID){
				$submit = false;
				//$return['error'][] = "User already Exists - ". $email_check['fullName']." - <a href='#' data-id='". $email_check['ID']. "' class='loaddetailspage'>click here to add them to the company</a>";
				$return['exists'] = $email_check['ID'];
			}

		}

		if ($ID=="" && $password==""){
			$submit = false;
			$return['error'][] = "You need to specify a password";
		}



		$values = array(
			"fullName"         => $fullName,
			"email"=> $email,
			"publications"     => $publications,
			"cID"=> $cID
		);


		if ($password)$values['password'] = $password;



//$values = $values['p']['p'];


		if ($submit){
			$passed_ID = $ID;
			$ID = user::save($ID, $values);

			// save to company here

			if ($passed_ID!=''){
				user::_add_company($ID, $cID);
			} else {
				user::_add_app($ID, $cID,"ab");
			}


			models\user_permissions::write($ID, $cID, $permissions);
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

}
