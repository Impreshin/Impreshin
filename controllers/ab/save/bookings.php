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


class bookings extends save {
	function __construct() {

		$user = F3::get("user");
		$userID = $user['ID'];
		if (!$userID) exit(json_encode(array("error" => F3::get("system")->error("U01"))));

	}

	function booking_delete() {
		$user = F3::get("user");
		$userID = $user['ID'];

		$ID = (isset($_GET['ID'])) ? $_GET['ID'] : "";
		$delete_reason = (isset($_POST['delete_reason'])) ? $_POST['delete_reason'] : "";

		$result = models\bookings::_delete($ID, $delete_reason);

		echo json_encode(array("result"=> $result));
		exit();
	}

	function form() {
		$user = F3::get("user");
		$userID = $user['ID'];

		//$accStuff = new models\accounts();
		//$accStuff= $accStuff->get($_POST['accNum']);

		$publication = new models\publications();
		$publication = $publication->get($user['pID']);


		$ID = (isset($_GET['ID'])&&$_GET['ID']&&$_GET['ID'] != "undefined") ? $_GET['ID'] : "";
		$type = (isset($_GET['type'])) ? $_GET['type'] : "";




		$details = new models\bookings();
		$details = $details->get($ID);



		$ID = $details['ID'];

		$values = array();

		$values['pID'] = $user['pID'];
		$values['dID'] = "";
		$values['typeID'] = $type;
		if ($ID) { // changes when editing
			if ($details['dateStatus'] != "0") { // not archived
				$values['checked'] = "0";
				$values['pageID'] = null;
			}
		} else { // when adding
			$values['userID'] = $userID;
		}
		//userName


		if (isset($_POST['client'])) $values['client'] = $_POST['client'];
		if (isset($_POST['colourID'])) $values['colourID'] = $_POST['colourID'];
		if (isset($_POST['colourSpot'])) $values['colourSpot'] = $_POST['colourSpot'];
		if (isset($_POST['col'])) $values['col'] = $_POST['col'];
		if (isset($_POST['cm'])) $values['cm'] = $_POST['cm'];
		if (isset($values['cm']) && isset($values['col'])) $values['totalspace'] = $values['cm'] * $values['col'];


		if (isset($_POST['rate'])) $values['rate'] = ($_POST['rate']) ? $_POST['rate'] : $_POST['rate_fld'];
		if (isset($_POST['totalCost'])) $values['totalCost'] = ($_POST['totalCost']) ? $_POST['totalCost'] : $_POST['totalShouldbe'];
		if (isset($_POST['totalShouldbe'])) $values['totalShouldbe'] = $_POST['totalShouldbe_e'];


		if (isset($_POST['discount'])) $values['discount'] = $_POST['discount'];
		if (isset($_POST['agencyDiscount'])) $values['agencyDiscount'] = $_POST['agencyDiscount'];


		if (isset($_POST['placingID'])) $values['placingID'] = $_POST['placingID'];

		if (isset($_POST['categoryID'])) $values['categoryID'] = $_POST['categoryID'];

		if (isset($_POST['InsertPO'])) $values['InsertPO'] = ($_POST['InsertPO']) ? $_POST['InsertPO'] : $publication['printOrder'];
		if (isset($_POST['rate']) && $type == "2") $values['InsertRate'] = ($_POST['rate']) ? $_POST['rate'] : $publication['InsertRate'];


		if (isset($_POST['accountID'])) $values['accountID'] = $_POST['accountID'];
		if (isset($_POST['orderNum'])) $values['orderNum'] = $_POST['orderNum'];
		if (isset($_POST['keyNum'])) $values['keyNum'] = $_POST['keyNum'];
		if (isset($_POST['remark'])) $values['remark'] = $_POST['remark'];
		if (isset($_POST['remarkTypeID'])) $values['remarkTypeID'] = $_POST['remarkTypeID'];

		if (isset($_POST['marketerID'])) $values['marketerID'] = $_POST['marketerID'];
		if (isset($_POST['insertTypeID'])) $values['insertTypeID'] = $_POST['insertTypeID'];


		//if (isset($_POST['accNum'])) $values['accNum'] = $_POST['accNum'];


		$ss = array();
		$ss["form"] = array(
			"type"      => $type,
			"last_marketer"      => $values['marketerID']
		);


		models\user_settings::save_setting($ss);


		//test_array($ID);

		$a = array();
		$b = array();
		foreach ($_POST['dID'] AS $date) {
			$v = $values;
			$v['dID'] = $date;

			$b[] = $v;
			$a[] = models\bookings::save($ID, $v);
		}


		$r = array();
		$r['ID'] = $ID;
		$r['values'] = $b;
		$r['post'] = $_POST;
		test_array($a);
		exit();


	}
	function repeat(){
		$user = F3::get("user");
		$userID = $user['ID'];
		$ID = (isset($_GET['ID'])) ? $_GET['ID'] : "";

		$dID = (isset($_POST['dID']))? $_POST['dID']:"";
		$exact_repeat = (isset($_POST['exact_repeat']))? $_POST['exact_repeat']:"0";


		if ($dID) {
			$response = models\bookings::repeat($ID, $dID, $exact_repeat);
		} else {
			$response = array();
		}



		test_array($response);
		exit();
	}


}
