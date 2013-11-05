<?php

namespace apps\nf\controllers\admin\save;


use \timer as timer;
use \apps\nf\models as models;
use \models\user as user;


class publications extends \apps\nf\controllers\save\save {
	function __construct() {
		parent::__construct();


	}

	function _save() {
		$user = $this->f3->get("user");
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];


		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$publication = isset($_POST['publication']) ? $_POST['publication'] : "";
		$InsertRate = isset($_POST['InsertRate']) ? $_POST['InsertRate'] : "";
		$printOrder = isset($_POST['printOrder']) ? $_POST['printOrder'] : "";
		$colours = isset($_POST['colours']) ? $_POST['colours'] : "";

		$columnsav = isset($_POST['columnsav']) ? $_POST['columnsav'] : "";
		$cmav = isset($_POST['cmav']) ? $_POST['cmav'] : "";
		$pagewidth = isset($_POST['pagewidth']) ? $_POST['pagewidth'] : "";




		$return = array(
			"error"   => array(),
			"ID"      => $ID

		);


		//test_array($p);
		$submit = true;

		if ($publication == "") {
			$submit = false;
			$return['error'][] = "Publication is Required";
		}

		if ($InsertRate==""){
			$submit = false;
			$return['error'][] = "Insert Rate is Required";
		} else {
			if (!is_numeric($InsertRate)){
				$submit = false;
				$return['error'][] = "Insert Rate must be a number";
			}
		}

		if ($printOrder==""){
			$submit = false;
			$return['error'][] = "Print Order is required";
		} else {
			if (!is_numeric($printOrder)) {
				$submit = false;
				$return['error'][] = "Print Order must be a number";
			}
		}

		if ($columnsav==""){
			$submit = false;
			$return['error'][] = "Columns is required";
		} else {
			if (!is_numeric($columnsav)) {
				$submit = false;
				$return['error'][] = "Columns must be a number";
			}
		}
		if ($cmav==""){
			$submit = false;
			$return['error'][] = "Cm is required";
		} else {
			if (!is_numeric($cmav)) {
				$submit = false;
				$return['error'][] = "Cm must be a number";
			}
		}

		if ($pagewidth==""){
			$submit = false;
			$return['error'][] = "Page Width is required";
		} else {
			if (!is_numeric($pagewidth)) {
				$submit = false;
				$return['error'][] = "Page Width must be a number";
			}
		}

		$passed_ID = $ID;
		$publish_date = isset($_POST['publish_date']) ? $_POST['publish_date'] : "";
		if ($passed_ID == "") {

			if ($publish_date == "") {
				$submit = false;
				$return['error'][] = "An initial date is required";
			}
		}







		$values = array(
			"cID"         => $cID,
			"publication"         => $publication,
			"InsertRate"     => $InsertRate,
			"printOrder"     => $printOrder,
			"columnsav"     => $columnsav,
			"cmav"     => $cmav,
			"pagewidth"     => $pagewidth,
			"colours"     => $colours,
		);





//$values = $values['p']['p'];


		if ($submit){



			$ID = \models\publications::save($ID, $values);

			$return['ID'] = $ID;



			if (($passed_ID == "" || $passed_ID == "undefined") && $publish_date && $ID != "") {
				$date_values = array(
					"pID"         => $ID,
					"publish_date"=> $publish_date,
					"current"     => '1',
				);
				$t = \models\dates::save("", $date_values);
			}
		}



	//	test_array(array("ID"=>$ID,"values"=>$values,"result"=>$return));


		return $GLOBALS["output"]['data'] = $return;

	}



	function _delete(){
		$user = $this->f3->get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";
		\models\publications::_delete($ID);
		return $GLOBALS["output"]['data'] = "done";

	}

	function _pub() {
		$user = $this->f3->get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$uID = $user['ID'];


		$p = new \DB\SQL\Mapper($this->f3->get("DB"),"nf_users_pub");
		$p->load("pID='$ID' and uID='$uID'");
		if (!$p->ID) {
			$p->uID = $uID;
			$p->pID = $ID;
			$p->save();
		} else {
			$p->erase();

		}


		return "done";
	}


}
