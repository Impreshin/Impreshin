<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace controllers\setup\save\ab;


use \timer as timer;
use \apps\ab\models as models;
use \models\user as user;


class placing {
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

		$placing = isset($_POST['placing']) ? $_POST['placing'] : "";
		$rate = isset($_POST['rate']) ? $_POST['rate'] : "";
		$colourID = isset($_POST['colourID']) ? $_POST['colourID'] : "";
		$sub_placing_IDs = isset($_POST['sub_placing_IDs']) ? $_POST['sub_placing_IDs'] : "";



		$return = array(
			"error"   => array(),
			"ID"      => $ID

		);


		//test_array($p);
		$submit = true;



		if ($placing==""){
			$submit = false;
			$return['error'][] = "Need to specify a Placing";
		}

		if ($rate==""){
			$submit = false;
			$return['error'][] = "Need to specify a Rate";
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
			"placing"         => $placing,
			"pID"         => $pID,
			"rate"     => $rate,
			"colourID"     => $colourID,
		);




//$values = $values['p']['p'];


		if ($submit){
			$passed_ID = $ID;
			$ID = models\placing::save($ID, $values);

			$return['ID'] = $ID;
		}

		$sub_placing = "";
		$sub_placing_IDs = explode(",", $sub_placing_IDs);

		$i = 1;
		foreach ($sub_placing_IDs as $record) {
			$spID = "";
			if (is_numeric($record)) $spID = $record;
			$sub_placing = array(
				"pID" => $pID,
				"placingID" => $ID,
				"label"     => isset($_POST['sub-label-' . $record]) ? $_POST['sub-label-' . $record] : "",
				"rate"      => isset($_POST['sub-rate-' . $record]) ? $_POST['sub-rate-' . $record] : "",
				"colourID"  => isset($_POST['sub-colourID-' . $record]) ? $_POST['sub-colourID-' . $record] : "",
				"orderby"   => $i++,
			);
			if ($spID && isset($_POST['sub-label-' . $record]) && $_POST['sub-label-' . $record] ==""){
				// delete
				models\sub_placing::_delete($spID);
			} else {
				if (isset($_POST['sub-label-' . $record]) && $_POST['sub-label-' . $record]){
					// save
					models\sub_placing::save($spID, $sub_placing);
				}

			}
		}



		//test_array($sub_placing);
	//	test_array(array("ID"=>$ID,"values"=>$values,"result"=>$return));


		return $GLOBALS["output"]['data'] = $return;

	}



	function _delete(){
		$user = $this->f3->get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";
		models\placing::_delete($ID);
		return $GLOBALS["output"]['data'] = "done";

	}

	function _sort() {
		$user = $this->f3->get("user");
		$pID = $_REQUEST['pID'];
		$cID = $_REQUEST['cID'];
		$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : "";
		$order = explode(",", $order);


		$i = 0;
		foreach ($order as $id) {
			$this->f3->get("DB")->exec("UPDATE ab_placing SET orderby = '$i' WHERE ID = '$id' AND pID = '$pID'");
			$i++;
		}


		return $GLOBALS["output"]['data'] = "done";

	}


}
