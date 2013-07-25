<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace controllers\setup\save\ab;


use \timer as timer;
use \apps\ab\models as models;
use \models\user as user;


class sections {
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

		$section = isset($_POST['section']) ? $_POST['section'] : "";
		$section_colour = isset($_POST['section_colour']) ? $_POST['section_colour'] : "";


		$return = array(
			"error"   => array(),
			"ID"      => $ID

		);


		//test_array($p);
		$submit = true;



		if ($section==""){
			$submit = false;
			$return['error'][] = "Need to specify a Section Name";
		}
		if ($section_colour==""){
			$submit = false;
			$return['error'][] = "Need to specify a Colour";
		}








		$values = array(
			"section"         => $section,
			"section_colour"=> $section_colour,
			"pID"=> $pID
		);





//$values = $values['p']['p'];


		if ($submit){
			$passed_ID = $ID;
			$ID = models\sections::save($ID, $values);

			$return['ID'] = $ID;
		}


	//	test_array(array("ID"=>$ID,"values"=>$values,"result"=>$return));


		return $GLOBALS["output"]['data'] = $return;

	}



	function _delete(){
		$user = $this->f3->get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";
		models\sections::_delete($ID);
		return $GLOBALS["output"]['data'] = "done";

	}

}
