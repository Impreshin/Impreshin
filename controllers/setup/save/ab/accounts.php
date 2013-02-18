<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace controllers\setup\save\ab;
use \F3 as F3;
use \Axon as Axon;
use \timer as timer;
use \models\ab as models;
use \models\user as user;


class accounts {
	function __construct() {
		$this->f3 = \base::instance();
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		if (!$userID) exit(json_encode(array("error" => $this->f3->get("system")->error("U01"))));

	}

	function _save() {
		$user = $this->f3->get("user");
		$pID = $_GET['pID'];
		$cID = $_GET['cID'];


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

	function _delete(){
		$user = $this->f3->get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		models\accounts::_delete($ID);
				return $GLOBALS["output"]['data'] = "done";



	}
	function _pub(){
		$user = $this->f3->get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$pID = $_GET['pID'];
		$cID = $_GET['cID'];





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
