<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\nf\controllers\admin\save;


use \timer as timer;
use \apps\nf\models as models;
use \models\user as user;


class users extends \apps\nf\controllers\save\save {
	function __construct() {
		parent::__construct();


	}

	function _save() {
		$user = $this->f3->get("user");
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];


		//test_array($user); 
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$fullName = isset($_POST['fullName']) ? $_POST['fullName'] : "";
		$email = isset($_POST['email']) ? strtolower($_POST['email']) : "";
		$password= isset($_POST['password']) ? $_POST['password'] : "";
		$publications= isset($_POST['publications']) ? $_POST['publications'] : array();
		$permissions= isset($_POST['permissions']) ? $_POST['permissions'] : array();

		$allow_setup= isset($_POST['allow_setup']) ? "1" :"0";
		$nf_author= isset($_POST['nf_author']) ? "1" :"0";



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
		if ($fullName==""){
			$submit = false;
			$return['error'][] = "You need to specify a Name";
		}
		if ($email==""){
			$submit = false;
			$return['error'][] = "You need to specify an Email";
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
				user::_add_app($ID, $cID,"nf");
			}






			//test_array($nf_author); 

			\apps\ab\app::permissions_save($ID, $cID, $permissions);
			$a = new \DB\SQL\Mapper($this->f3->get("DB"),"global_users_company");
			$a->load("uID='$ID' AND cID = '$cID'");
			$a->allow_setup = $allow_setup;
			$a->nf_author = $nf_author;
			$a->save();

			$return['ID'] = $ID;
		}


	//	test_array(array("ID"=>$ID,"values"=>$values,"result"=>$return));


		return $GLOBALS["output"]['data'] = $return;

	}
	function add_company(){
		$user = $this->f3->get("user");
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";


		user::_add_company($ID, $cID);
		return $GLOBALS["output"]['data'] = array("ID"=>$ID);
	}
	function add_app(){
		$user = $this->f3->get("user");
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];

		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$app = $this->f3->get("app");

		user::_add_app($ID, $cID, $app);
		return $GLOBALS["output"]['data'] = array("ID"=>$ID);
	}
	function remove_app(){
		$user = $this->f3->get("user");
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];

		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$app = $this->f3->get("app");

		user::_remove_app($ID, $cID, $app);
		return $GLOBALS["output"]['data'] = array("ID"=>$ID);
	}
	function _delete(){
		$user = $this->f3->get("user");
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$a = new \DB\SQL\Mapper($this->f3->get("DB"),"global_users_company");
		$a->load("uID='$ID' AND cID = '$cID'");

		$a->erase();

		return "done";


	}

	function _pub() {
		$user = $this->f3->get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$cID = $user['company']['ID'];

		

		$p = new \DB\SQL\Mapper($this->f3->get("DB"),"global_users_company");
		$p->load("uID='$ID' and cID='$cID'");
		
		
		if ($p->ID) {
			$p->nf_author = ($p->nf_author==1)?0:1;
			$p->save();
		}


		return "done";
	}
}
