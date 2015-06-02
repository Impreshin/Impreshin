<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\ab\controllers\save;


use \timer as timer;
use \apps\ab\models as models;
use \models\user as user;


class bookings extends save {
	function __construct() {
		parent::__construct();


	}

	function booking_delete() {
		$user = $this->f3->get("user");
		$userID = $user['ID'];

		$ID = (isset($_GET['ID'])) ? $_GET['ID'] : "";
		$delete_reason = (isset($_POST['delete_reason'])) ? $_POST['delete_reason'] : "";

		$result = models\bookings::_delete($ID, $delete_reason);

		echo json_encode(array("result"=> $result));
		exit();
	}

	function form() {
		$user = $this->f3->get("user");
		$userID = $user['ID'];

		//$accStuff = new models\accounts();
		//$accStuff= $accStuff->get($_POST['accNum']);

		$publication = new \models\publications();
		$publication = $publication->get($user['pID']);


		$ID = (isset($_GET['ID'])&&$_GET['ID']&&$_GET['ID'] != "undefined") ? $_GET['ID'] : "";
		$type = (isset($_GET['type'])) ? $_GET['type'] : "";




		$details = new models\bookings();
		$details = $details->get($ID);


		//test_array($details); 

		$ID = $details['ID'];

		$values = array();

		$values['pID'] = $user['pID'];
		$values['dID'] = "";
		$values['typeID'] = $type;
		if ($ID) { // changes when editing
			if ($details['dateStatus'] != "0"  ) { // not archived
				if (!$user['permissions']['form']['edit_master']){
					$values['checked'] = "0";
					
				}
				if ($details['pageID']){
					
					$a = new \DB\SQL\Mapper($this->f3->get("DB"),"global_pages");
					$a->load("ID = '{$details['pageID']}'");
					$a->ab_locked="0";
					$a->save();
				}
				
				
				$values['pageID'] = null;
				$values['x_offset'] = null;
				$values['y_offset'] = null;
			}
		} else { // when adding
			$values['userID'] = $userID;
		}
		//userName


		if (isset($_POST['client'])) $values['client'] = $_POST['client'];




		if (isset($_POST['rate'])) $values['rate'] = ($_POST['rate']) ? $_POST['rate'] : $_POST['rate_fld'];
		if (isset($_POST['totalCost'])) $values['totalCost'] = ($_POST['totalCost']) ? $_POST['totalCost'] : $_POST['totalShouldbe'];
		if (isset($_POST['totalShouldbe'])) $values['totalShouldbe'] = $_POST['totalShouldbe_e'];


		if (isset($_POST['discount'])) $values['discount'] = $_POST['discount'];
		if (isset($_POST['agencyDiscount'])) $values['agencyDiscount'] = $_POST['agencyDiscount'];

		if (isset($_POST['payment_methodID'])) {
			$values['payment_methodID'] = $_POST['payment_methodID'];
			if ($_POST['payment_methodID']!='1'){
				if (isset($_POST['payment_method_note'])) $values['payment_method_note'] = $_POST['payment_method_note'];
			} else {
				$values['payment_method_note'] = "";
			}
		}



		switch ($type) {
			case 1:
				if (isset($_POST['col'])) $values['col'] = $_POST['col'];
				if (isset($_POST['cm'])) $values['cm'] = $_POST['cm'];
				if (isset($values['cm']) && isset($values['col'])) $values['totalspace'] = $values['cm'] * $values['col'];
				if (isset($_POST['placingID'])) $values['placingID'] = $_POST['placingID'];
				if (isset($_POST['sub_placingID'])) $values['sub_placingID'] = $_POST['sub_placingID'];
				if (isset($_POST['colourID'])) $values['colourID'] = $_POST['colourID'];
				break;
			case 2:
				if (isset($_POST['InsertPO'])) $values['InsertPO'] = ($_POST['InsertPO']) ? $_POST['InsertPO'] : $publication['printOrder'];
				if (isset($_POST['insertTypeID'])) $values['insertTypeID'] = $_POST['insertTypeID'];
				$values['placingID'] = null;
				$values['sub_placingID'] = null;
				$values['colourID'] = null;
				$values['cm'] = null;
				$values['col'] = null;
				$values['pageID'] = null;
				$values['x_offset'] = null;
				$values['y_offset'] = null;

				if (isset($_POST['rate'])) $values['InsertRate'] = ($_POST['rate']) ? $_POST['rate'] : $publication['InsertRate'];
				
				break;
			case 3:
				if (isset($_POST['classifiedText'])) $values['classifiedText'] = ($_POST['classifiedText']) ? $_POST['classifiedText'] : "";
				if (isset($_POST['classifiedTypeID'])) $values['classifiedTypeID'] = $_POST['classifiedTypeID'];
				if (isset($_POST['classifiedWords'])) $values['classifiedWords'] = $_POST['classifiedWords']? $_POST['classifiedWords'] : 0;
				if (isset($_POST['classifiedCharacters'])) $values['classifiedCharacters'] = $_POST['classifiedCharacters']? $_POST['classifiedCharacters']:0;
				if (isset($_POST['classifiedMediaName'])) $values['classifiedMediaName'] = $_POST['classifiedMediaName']? $_POST['classifiedMediaName']:"";
				if (isset($_POST['classifiedMedia'])) $values['classifiedMedia'] = $_POST['classifiedMedia']? $_POST['classifiedMedia']:"";
				$values['placingID'] = null;
				$values['sub_placingID'] = null;
				$values['colourID'] = null;
				$values['cm'] = null;
				$values['col'] = null;
				$values['pageID'] = null;
				$values['x_offset'] = null;
				$values['y_offset'] = null;
				$defaultClassifiedRate = 123;
				
				
				if (isset($_POST['rate'])) $values['classifiedRate'] = ($_POST['rate']) ? $_POST['rate'] : $defaultClassifiedRate;
				
				break;
		}




		if (isset($_POST['categoryID'])) $values['categoryID'] = $_POST['categoryID'];


		
		


		if (isset($_POST['accountID'])) $values['accountID'] = $_POST['accountID'];
		if (isset($_POST['orderNum'])) $values['orderNum'] = $_POST['orderNum'];
		if (isset($_POST['keyNum'])) $values['keyNum'] = $_POST['keyNum'];
		if (isset($_POST['remark'])) $values['remark'] = $_POST['remark'];
		if (isset($_POST['remarkTypeID'])) $values['remarkTypeID'] = $_POST['remarkTypeID'];

		if (isset($_POST['marketerID'])) $values['marketerID'] = $_POST['marketerID'];



		//if (isset($_POST['accNum'])) $values['accNum'] = $_POST['accNum'];


		$ss = array();
		$ss["form"] = array(
			"type"      => $type,
			"last_marketer"      => ($values['marketerID']) ? $values['marketerID'] : "",
			"last_category"      => ($values['categoryID'])? $values['categoryID'] : ""
		);
		$values['deleted']=NULL;
		$values['deleted_userID']= NULL;
		$values['deleted_user']= NULL;
		$values['deleted_date']= NULL;
		$values['deleted_reason']= NULL;

		models\settings::save($ss);


		//test_array($ID);

		$a = array();
		$b = array();
		//test_array($values);
		foreach ($_POST['dID'] AS $date) {
			$v = $values;
			$v['dID'] = $date;

			$b[] = $v;
			array_walk_recursive($v, "form_write");
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
		$user = $this->f3->get("user");
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
