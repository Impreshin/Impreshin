<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\ab\controllers\save;


use \timer as timer;
use \apps\ab\models as models;
use \models\user as user;


class admin_marketers_targets extends save {
	function __construct() {
		parent::__construct();


	}

	function _save() {
		$user = $this->f3->get("user");
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];


		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";
		$mID = isset($_REQUEST['mID']) ? $_REQUEST['mID'] : "";
		if (!$mID || $mID=="undefined") {
			$mID = isset($user['marketer']['ID']) ? $user['marketer']['ID'] : "";
		}

			$target = isset($_POST['target']) ? $_POST['target'] : "";
		$publications = isset($_POST['publications']) ? $_POST['publications'] : array();
		$date_from = isset($_POST['date_from']) ? $_POST['date_from'] : "";
		$date_to = isset($_POST['date_to']) ? $_POST['date_to'] : "";
		$locked = isset($_POST['locked']) ? $_POST['locked'] : "0";



		//test_array($mID);

		$return = array(
			"error"   => array(),
			"ID"      => $ID

		);


		//test_array($p);
		$submit = true;

		if ($target == "") {
			$submit = false;
			$return['error'][] = "Need to specify a Target";
		} else {
			if (!is_numeric($target)) {
				$submit = false;
				$return['error'][] = "Target must be a number";
			}
		}

		if ($date_from == "") {
			$submit = false;
			$return['error'][] = "Need to specify a Date From";
		}
		if ($date_to == "") {
			$submit = false;
			$return['error'][] = "Need to specify a Date To";
		}

		if ($mID == "") {
			$submit = false;
			$return['error'][] = "No marketer selected";
		}

		$values = array(
			"mID"=>$mID,
			"target"         => $target,
			"publications"     => $publications,
			"date_from"              => $date_from,
			"date_to"              => $date_to,
			"locked"              => $locked
		);



//$values = $values['p']['p'];


		if ($submit) {

			$dates = array(
				date("Y-m-d",strtotime($date_from)),
				date("Y-m-d",strtotime($date_to)),
			);


			sort($dates);

			$values['date_from'] = $dates[0];
			$values['date_to'] = $dates[1];

			$passed_ID = $ID;
			$ID = models\marketers_targets::save($ID, $values);

			$return['ID'] = $ID;
		}


		//	test_array(array("ID"=>$ID,"values"=>$values,"result"=>$return));


		return $GLOBALS["output"]['data'] = $return;

	}


	function _delete() {
		$user = $this->f3->get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";
		models\marketers_targets::_delete($ID);
		return $GLOBALS["output"]['data'] = "done";

	}

	function _pub() {
		$user = $this->f3->get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$pID = $user['publication']['ID'];


		$p = new \DB\SQL\Mapper($this->f3->get("DB"),"ab_marketers_targets_pub");
		$p->load("mtID='$ID' and pID='$pID'");
		if (!$p->ID) {
			$p->mtID = $ID;
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

		\models\logging::save("marketers_targets", $changes, $label);
		return $changes;

	}


}
