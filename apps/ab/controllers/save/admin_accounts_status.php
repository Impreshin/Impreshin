<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\ab\controllers\save;


use \timer as timer;
use \apps\ab\models as models;
use \models\user as user;


class admin_accounts_status extends save {
	function __construct() {
		parent::__construct();


	}

	function _save() {
		$user = $this->f3->get("user");
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];


		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$status = isset($_POST['status']) ? $_POST['status'] : "";
		$blocked = isset($_POST['blocked']) ? $_POST['blocked'] : "0";
		$labelClass= isset($_POST['labelClass']) ? $_POST['labelClass'] : "";


		$return = array(
			"error"   => array(),
			"ID"      => $ID

		);


		//test_array($p);
		$submit = true;



		if ($status==""){
			$submit = false;
			$return['error'][] = "Need to specify a Status";
		}








		$values = array(
			"status"         => $status,
			"blocked"=> $blocked,
			"labelClass"=> $labelClass,
			"cID"=> $cID
		);





//$values = $values['p']['p'];


		if ($submit){
			$passed_ID = $ID;
			$ID = models\accountStatus::save($ID, $values);

			$return['ID'] = $ID;
		}


	//	test_array(array("ID"=>$ID,"values"=>$values,"result"=>$return));


		return $GLOBALS["output"]['data'] = $return;

	}

	function _sort() {
		$user = $this->f3->get("user");
		$cID = $user['company']['ID'];
		$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : "";
		$order = explode(",",$order);


		$i=0;
		foreach($order as $id){
			$this->f3->get("DB")->exec("UPDATE ab_accounts_status SET orderby = '$i' WHERE ID = '$id' AND cID = '$cID'");
				$i++;
		}


		return $GLOBALS["output"]['data'] = "done";

	}

	function _delete(){
		$user = $this->f3->get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";
		models\accountStatus::_delete($ID);
		return $GLOBALS["output"]['data'] = "done";

	}

}
