<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\ab\controllers\save;


use \timer as timer;
use \apps\ab\models as models;
use \models\user as user;


class admin_inserts_types extends save {
	function __construct() {
		parent::__construct();


	}

	function _save() {
		$user = $this->f3->get("user");
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];


		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$insertsLabel = isset($_POST['insertsLabel']) ? $_POST['insertsLabel'] : "";
		$rate = isset($_POST['rate']) ? $_POST['rate'] : "";




		$return = array(
			"error"   => array(),
			"ID"      => $ID

		);


		//test_array($p);
		$submit = true;



		if ($insertsLabel==""){
			$submit = false;
			$return['error'][] = "Need to specify a Label";
		}


		if ($rate == "") {
			$submit = false;
			$return['error'][] = "Need to specify a Rate";
		} else {
			if (!is_numeric($rate)) {
				$submit = false;
				$return['error'][] = "Rate must be a number";
			}
		}







		$values = array(
			"insertsLabel"         => $insertsLabel,
			"pID"         => $pID,
			"rate"     => $rate,
		);





//$values = $values['p']['p'];


		if ($submit){
			$passed_ID = $ID;
			$ID = models\inserts_types::save($ID, $values);

			$return['ID'] = $ID;
		}


	//	test_array(array("ID"=>$ID,"values"=>$values,"result"=>$return));


		return $GLOBALS["output"]['data'] = $return;

	}



	function _delete(){
		$user = $this->f3->get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";
		models\inserts_types::_delete($ID);
		return $GLOBALS["output"]['data'] = "done";

	}

	function _sort() {
		$user = $this->f3->get("user");
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];
		$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : "";
		$order = explode(",", $order);


		$i = 0;
		foreach ($order as $id) {
			$this->f3->get("DB")->exec("UPDATE ab_inserts_types SET orderby = '$i' WHERE ID = '$id' AND pID = '$pID'");
			$i++;
		}


		return $GLOBALS["output"]['data'] = "done";

	}

	function _copyfrom() {
		$user = $this->f3->get("user");
		$cID = $user['company']['ID'];
		$pID = (isset($_GET['new_pID'])) ? $_GET['new_pID'] : $user['publication']['ID'];
		$oldpID = isset($_REQUEST['pID']) ? $_REQUEST['pID'] : "";


		models\inserts_types::copyfrom($pID,$oldpID);




		return $GLOBALS["output"]['data'] = "done";

	}


}
