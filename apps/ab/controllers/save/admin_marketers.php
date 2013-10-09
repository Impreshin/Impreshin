<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\ab\controllers\save;


use \timer as timer;
use \apps\ab\models as models;
use \models\user as user;


class admin_marketers extends save {
	function __construct() {
		parent::__construct();


	}

	function _save() {
		$user = $this->f3->get("user");
		$pID = $user['publication']['ID'];
		$cID = $user['publication']['cID'];


		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$marketer = isset($_POST['marketer']) ? $_POST['marketer'] : "";
		$number = isset($_POST['number']) ? $_POST['number'] : "";
		$email = isset($_POST['email']) ? $_POST['email'] : "";
		$uID = isset($_POST['uID']) ? $_POST['uID'] : "";
		$publications = isset($_POST['publications']) ? $_POST['publications'] : array();
		$code = isset($_POST['code']) ? $_POST['code'] : array();


		$return = array(
			"error"   => array(),
			"ID"      => $ID

		);


		//test_array($p);
		$submit = true;



		if ($marketer==""){
			$submit = false;
			$return['error'][] = "Need to specify a Marketer Name";
		}









		$values = array(
			"marketer"         => $marketer,
			"number"         => $number,
			"email"         => $email,
			"uID"         => $uID,
			"publications"     => $publications,
			"code"     => $code,
			"cID"=> $cID
		);





//$values = $values['p']['p'];


		if ($submit){
			$passed_ID = $ID;
			$ID = models\marketers::save($ID, $values);

			$return['ID'] = $ID;
		}


	//	test_array(array("ID"=>$ID,"values"=>$values,"result"=>$return));


		return $GLOBALS["output"]['data'] = $return;

	}



	function _delete(){
		$user = $this->f3->get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";
		models\marketers::_delete($ID);
		return $GLOBALS["output"]['data'] = "done";

	}

	function _pub() {
		$user = $this->f3->get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$pID = $user['publication']['ID'];


		$p = new \DB\SQL\Mapper($this->f3->get("DB"),"ab_marketers_pub");
		$p->load("mID='$ID' and pID='$pID'");
		if (!$p->ID) {
			$p->mID = $ID;
			$p->pID = $pID;
			$p->save();
			$pub = "Added: " . $user['publication']['publication'];
		} else {
			$p->erase();
			$pub = "Removed: " . $user['publication']['publication'];

		}

		$changes = array(
			array(
				"k"=> "publication",
				"v"=> $pub,
				"w"=> '-'
			)
		);

		$a = new \DB\SQL\Mapper($this->f3->get("DB"),"ab_marketers");
		$a->load("ID='$ID'");
		$label = "";
		if ($a->ID) {
			$label = "Record Edited ($a->marketer)";
		}

		\models\logging::save("marketers", $changes, $label);
		return $changes;

	}


}
