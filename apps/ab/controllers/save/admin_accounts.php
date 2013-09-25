<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\ab\controllers\save;



use \timer as timer;
use \apps\ab\models as models;
use \models\user as user;


class admin_accounts extends save {
	function __construct() {
		parent::__construct();


	}

	function _save() {
		$user = $this->f3->get("user");
		$pID = $user['publication']['ID'];
		$cID = $user['publication']['cID'];


		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$account = isset($_POST['account']) ? $_POST['account'] : "";
		$accNum = isset($_POST['accNum']) ? $_POST['accNum'] : "";
		$statusID= isset($_POST['statusID']) ? $_POST['statusID'] : "";
		$remark = isset($_POST['remark']) ? $_POST['remark'] : "";
		$email = isset($_POST['email']) ? $_POST['email'] : "";
		$phone = isset($_POST['phone']) ? $_POST['phone'] : "";
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
			"phone"=> $phone,
			"email"=> $email,
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
		$user = $this->f3->get("user");
		$cID = $user['publication']['cID'];

		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";


		user::_add_company($ID, $cID);
		return $GLOBALS["output"]['data'] = array("ID"=>$ID);
	}
	function add_app(){
		$user = $this->f3->get("user");
		$cID = $user['publication']['cID'];

		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$app = $this->f3->get("app");

		user::_add_app($ID, $cID, $app);
		return $GLOBALS["output"]['data'] = array("ID"=>$ID);
	}
	function remove_app(){
		$user = $this->f3->get("user");
		$cID = $user['publication']['cID'];

		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$app = $this->f3->get("app");

		user::_remove_app($ID, $cID, $app);
		return $GLOBALS["output"]['data'] = array("ID"=>$ID);
	}
	function _delete(){
		$user = $this->f3->get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		models\accounts::_delete($ID);
				return $GLOBALS["output"]['data'] = "done";



	}
	function _pub(){
		$user = $this->f3->get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$pID = $user['publication']['ID'];





		$p = new  \DB\SQL\Mapper($this->f3->get("DB"),"ab_accounts_pub");
		$p->load("aID='$ID' and pID='$pID'");
		if (!$p->ID){
			$p->aID=$ID;
			$p->pID=$pID;
			$p->save();
			$pub = "Added: ".$user['publication']['publication'];
		} else {
			$p->erase();
			$pub = "Removed: " . $user['publication']['publication'];
		}

		$changes = array(
			array(
				"k"=>"publication",
				"v"=>$pub,
				"w"=>'-'
			)
		);

		$a = new \DB\SQL\Mapper($this->f3->get("DB"),"ab_accounts");
		$a->load("ID='$ID'");
$label = "";
		if ($a->ID) {
			$label = "Record Edited ($a->account)";
		}

		\models\logging::save("accounts", $changes, $label);
		return $changes;
	}

}
