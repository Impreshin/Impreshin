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


class admin_loading extends save {
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

		$pages = isset($_POST['pages']) ? $_POST['pages'] : "";
		$percent = isset($_POST['percent']) ? $_POST['percent'] : "";




		$return = array(
			"error"   => array(),
			"ID"      => $ID

		);


		//test_array($p);
		$submit = true;



		if ($pages==""){
			$submit = false;
			$return['error'][] = "Pages is Required";
		} else {
			if (!is_numeric($pages)){
				$submit = false;
				$return['error'][] = "Pages must be a number";
			}
		}

		if ($percent==""){
			$submit = false;
			$return['error'][] = "Loading % is required";
		} else {
			if (!is_numeric($percent)) {
				$submit = false;
				$return['error'][] = "Loading % must be a number";
			}
		}









		$values = array(
			"pages"         => $pages,
			"pID"         => $pID,
			"percent"     => $percent,
		);





//$values = $values['p']['p'];


		if ($submit){
			$passed_ID = $ID;
			$ID = models\loading::save($ID, $values);

			$return['ID'] = $ID;
		}


	//	test_array(array("ID"=>$ID,"values"=>$values,"result"=>$return));


		return $GLOBALS["output"]['data'] = $return;

	}



	function _delete(){
		$user = $this->f3->get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";
		models\loading::_delete($ID);
		return $GLOBALS["output"]['data'] = "done";

	}

	function _copyfrom() {
		$user = $this->f3->get("user");
		$cID = $user['publication']['cID'];
		$pID = (isset($_GET['new_pID'])) ? $_GET['new_pID'] : $user['publication']['ID'];
		$oldpID = isset($_REQUEST['pID']) ? $_REQUEST['pID'] : "";


		models\loading::copyfrom($pID, $oldpID);


		return $GLOBALS["output"]['data'] = "done";

	}



}
