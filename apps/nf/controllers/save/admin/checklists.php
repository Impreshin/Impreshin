<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\nf\controllers\save\admin;


use \timer as timer;
use \apps\nf\models as models;
use \models\user as user;


class checklists extends \apps\nf\controllers\save\save {
	function __construct() {
		parent::__construct();


	}

	function _save() {
		$user = $this->f3->get("user");
		$pID = $user['publication']['ID'];
		$cID = $user['publication']['cID'];


		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$label = isset($_POST['label']) ? $_POST['label'] : "";
		$description = isset($_POST['description']) ? $_POST['description'] : array();
		$categoryID = isset($_REQUEST['categoryID']) ? $_REQUEST['categoryID'] : array();


		$return = array(
			"error"   => array(),
			"ID"      => $ID

		);


		//test_array($p);
		$submit = true;



		if ($label==""){
			$submit = false;
			$return['error'][] = "Need to specify a label";
		}









		$values = array(
			"label"         => $label,
			"description"     => $description,
			"cID"=> $cID,
			"categoryID"=> $categoryID
		);





//$values = $values['p']['p'];


		if ($submit){
			$passed_ID = $ID;
			$ID = models\checklists::save($ID, $values);

			$return['ID'] = $ID;
		}


	//	test_array(array("ID"=>$ID,"values"=>$values,"result"=>$return));


		return $GLOBALS["output"]['data'] = $return;

	}



	function _delete(){
		$user = $this->f3->get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";
		models\checklists::_delete($ID);
		return $GLOBALS["output"]['data'] = "done";

	}

	function _sort() {
		$user = $this->f3->get("user");
		$categoryID = isset($_REQUEST['categoryID']) ? $_REQUEST['categoryID'] : "";
		$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : "";
		$order = explode(",", $order);


		$i = 0;
		foreach ($order as $id) {
			$this->f3->get("DB")->exec("UPDATE nf_checklists SET orderby = '$i' WHERE ID = '$id' AND categoryID = '$categoryID'");
			$i++;
		}


		return $GLOBALS["output"]['data'] = "done";

	}

	
}
