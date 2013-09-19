<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\nf\controllers\save\admin;


use \timer as timer;
use \apps\nf\models as models;
use \models\user as user;


class priorities extends \apps\nf\controllers\save\save {
	function __construct() {
		parent::__construct();


	}

	function _save() {
		$user = $this->f3->get("user");
		$pID = $user['publication']['ID'];
		$cID = $user['publication']['cID'];


		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$priority = isset($_POST['priority']) ? $_POST['priority'] : "";


		$return = array(
			"error"   => array(),
			"ID"      => $ID

		);


		//test_array($p);
		$submit = true;



		if ($priority==""){
			$submit = false;
			$return['error'][] = "Need to specify a label";
		}









		$values = array(
			"priority"         => $priority,
			"cID"=> $cID,
		);





//$values = $values['p']['p'];


		if ($submit){
			$passed_ID = $ID;
			$ID = models\priorities::save($ID, $values);

			$return['ID'] = $ID;
		}


	//	test_array(array("ID"=>$ID,"values"=>$values,"result"=>$return));


		return $GLOBALS["output"]['data'] = $return;

	}



	function _delete(){
		$user = $this->f3->get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";
		models\priorities::_delete($ID);
		return $GLOBALS["output"]['data'] = "done";

	}

	function _sort() {
		$user = $this->f3->get("user");
		$cID = $user['company']['ID'];
		$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : "";
		$order = explode(",", $order);


		$i = 0;
		foreach ($order as $id) {
			$this->f3->get("DB")->exec("UPDATE nf_priorities SET orderby = '$i' WHERE ID = '$id' AND cID ='$cID'");
			$i++;
		}


		return $GLOBALS["output"]['data'] = "done";

	}

	
}
