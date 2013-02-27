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


class admin_placing_colours extends save {
	function __construct() {
		$this->f3 = \base::instance();
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		if (!$userID) exit(json_encode(array("error" => $this->f3->get("system")->error("U01"))));

	}

	function _save() {
		$user = $this->f3->get("user");
		$pID = $user['publication']['ID'];
		$cID = $user['publication']['cID'];


		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$label = isset($_POST['label']) ? $_POST['label'] : "";
		$colour = isset($_POST['colour']) ? $_POST['colour'] : array();
		$rate = isset($_POST['rate']) ? $_POST['rate'] : array();
		$placingID = isset($_REQUEST['placingID']) ? $_REQUEST['placingID'] : array();


		$return = array(
			"error"   => array(),
			"ID"      => $ID

		);


		//test_array($p);
		$submit = true;


		if ($label == "") {
			$submit = false;
			$return['error'][] = "Label is Required";
		}
		if ($colour == "") {
			$submit = false;
			$return['error'][] = "Colour is Required";
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
			"label"         => $label,
			"colour"     => $colour,
			"rate"     => $rate,
			"placingID"=> $placingID,
			"pID"=> $pID
		);





//$values = $values['p']['p'];


		if ($submit){
			$passed_ID = $ID;
			$ID = models\colours::save($ID, $values);

			$return['ID'] = $ID;
		}


	//	test_array(array("ID"=>$ID,"values"=>$values,"result"=>$return));


		return $GLOBALS["output"]['data'] = $return;

	}



	function _delete(){
		$user = $this->f3->get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";
		models\colours::_delete($ID);
		return $GLOBALS["output"]['data'] = "done";

	}

	function _sort() {
		$user = $this->f3->get("user");
		$cID = $user['publication']['cID'];
		$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : "";
		$placingID = isset($_REQUEST['placingID']) ? $_REQUEST['placingID'] : "";
		$order = explode(",", $order);


		$i = 0;
		foreach ($order as $id) {
			$this->f3->get("DB")->exec("UPDATE ab_placing_sub SET orderby = '$i' WHERE ID = '$id' AND placingID = '$placingID'");
			$i++;
		}


		return $GLOBALS["output"]['data'] = "done";

	}


}
