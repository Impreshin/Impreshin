<?php

namespace apps\cm\controllers\admin\save;


use \timer as timer;
use \apps\cm\models as models;
use \models\user as user;


class details_types extends \apps\cm\controllers\save\save {
	function __construct() {
		parent::__construct();


	}

	function _save() {
		$user = $this->f3->get("user");
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];


		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$label = isset($_POST['label']) ? $_POST['label'] : "";
		$group = isset($_POST['group']) ? $_POST['group'] : "";
		$type = isset($_POST['type']) ? $_POST['type'] : "";


		$return = array(
			"error"   => array(),
			"ID"      => $ID

		);


		//test_array($p);
		$submit = true;



		if ($label==""){
			$submit = false;
			$return['error'][] = "Need to specify a Label Name";
		}
		if ($type==""){
			$submit = false;
			$return['error'][] = "Need to specify a type";
		}









		$values = array(
			"label"         => $label,
			"group"         => $group,
			"type"         => $type,
			"cID"=> $cID
		);





//$values = $values['p']['p'];


		if ($submit){
			$passed_ID = $ID;
			$ID = models\details_types::save($ID, $values);

			$return['ID'] = $ID;
		}


	//	test_array(array("ID"=>$ID,"values"=>$values,"result"=>$return));


		return $GLOBALS["output"]['data'] = $return;

	}



	function _delete(){
		$user = $this->f3->get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";
		models\details_types::_delete($ID);
		return $GLOBALS["output"]['data'] = "done";

	}

	function _sort() {
		$user = $this->f3->get("user");
		$cID = $user['company']['ID'];
		$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : "";
		$order = explode(",", $order);


		$i = 100;
		foreach ($order as $id) {
			$this->f3->get("DB")->exec("UPDATE cm_details_types SET orderby = '$i' WHERE ID = '$id' AND cID = '$cID'");
			$i++;
		}


		return $GLOBALS["output"]['data'] = "done";

	}

	
}
