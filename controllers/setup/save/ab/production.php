<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace controllers\setup\save\ab;


use \timer as timer;
use \apps\ab\models as models;
use \models\user as user;


class production {
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

		$production = isset($_POST['production']) ? $_POST['production'] : "";

		$uID = isset($_POST['uID']) ? $_POST['uID'] : "";
		$publications = isset($_POST['publications']) ? $_POST['publications'] : array();


		$return = array(
			"error"   => array(),
			"ID"      => $ID

		);


		//test_array($p);
		$submit = true;



		if ($production==""){
			$submit = false;
			$return['error'][] = "Need to specify a Production Name";
		}









		$values = array(
			"production"         => $production,
			"uID"         => $uID,
			"publications"     => $publications,
			"cID"=> $cID
		);





//$values = $values['p']['p'];


		if ($submit){
			$passed_ID = $ID;
			$ID = models\production::save($ID, $values);

			$return['ID'] = $ID;
		}


	//	test_array(array("ID"=>$ID,"values"=>$values,"result"=>$return));


		return $GLOBALS["output"]['data'] = $return;

	}



	function _delete(){
		$user = $this->f3->get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";
		models\production::_delete($ID);
		return $GLOBALS["output"]['data'] = "done";

	}

	function _pub() {
		$user = $this->f3->get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$pID = $_GET['pID'];
		$cID = $_GET['cID'];


		$p = new \DB\SQL\Mapper($this->f3->get("DB"),"ab_production_pub");
		$p->load("productionID='$ID' and pID='$pID'");
		if (!$p->ID) {
			$p->productionID = $ID;
			$p->pID = $pID;
			$p->save();
		} else {
			$p->erase();

		}


		return "done";
	}


}
