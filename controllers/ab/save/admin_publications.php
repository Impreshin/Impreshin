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


class admin_publications extends save {
	function __construct() {

		$user = F3::get("user");
		$userID = $user['ID'];
		if (!$userID) exit(json_encode(array("error" => F3::get("system")->error("U01"))));

	}

	function _save() {
		$user = F3::get("user");
		$pID = $user['publication']['ID'];
		$cID = $user['publication']['cID'];


		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$publication = isset($_POST['publication']) ? $_POST['publication'] : "";
		$InsertRate = isset($_POST['InsertRate']) ? $_POST['InsertRate'] : "";
		$printOrder = isset($_POST['printOrder']) ? $_POST['printOrder'] : "";

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









		$values = array(
			"publication"         => $publication,
			"InsertRate"     => $InsertRate,
			"printOrder"     => $printOrder,
			"columnsav"     => $columnsav,
			"cmav"     => $cmav,
			"pagewidth"     => $pagewidth,
		);





//$values = $values['p']['p'];


		if ($submit){
			$passed_ID = $ID;
			$ID = models\publications::save($ID, $values);

			$return['ID'] = $ID;
		}


	//	test_array(array("ID"=>$ID,"values"=>$values,"result"=>$return));


		return $GLOBALS["output"]['data'] = $return;

	}



	function _delete(){
		$user = F3::get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";
		models\publications::_delete($ID);
		return $GLOBALS["output"]['data'] = "done";

	}




}
