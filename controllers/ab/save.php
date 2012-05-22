<?php
namespace controllers\ab;
use \F3 as F3;
use \timer as timer;
use models\ab as models;
use models\ab\bookings as bookings;
use models\ab\dates as dates;
use models\ab\loading as loading;
use models\ab\publications as publications;
use models\user as user;

class save {

	function __construct() {

		$user = F3::get("user");
		$userID = $user['ID'];
		if (!$userID) exit(json_encode(array("error" => F3::get("system")->error("U01"))));
	}

	function __destruct() {


	}


	function list_settings(){
		$user = F3::get("user");
		$userID = $user['ID'];

		$reset = (isset($_GET['reset'])) ? explode(",",$_GET['reset']) : array();
		$ab_defaults = F3::get("ab_defaults");

		$columns = (!in_array("columns", $reset))? (isset($_POST['columns']))?explode(",",$_POST['columns']): $ab_defaults["list"]['col']: $ab_defaults['list']['col'];
		$group = (!in_array("group", $reset)) ?(isset($_POST['group']))?$_POST['group']: $ab_defaults['list']['group']['g'] : $ab_defaults['list']['group']['g'];
		$order = (!in_array("order", $reset)) ?(isset($_POST['order']))?$_POST['order']: $ab_defaults['list']['group']['o'] : $ab_defaults['list']['group']['o'];



		$new = array();
		$new["col"] = $columns;
		$new["count"] = count($columns);
		$new["group"] = array(
			"g"=> $group,
			"o"=> $order
		);
		//$new["order"] = $order;


		$values = array();
		$values['list']=$new;




		user::save_setting($values);



		echo json_encode(array("result"=> "1"));
		exit();
	}
	function form(){
		$user = F3::get("user");
		$userID = $user['ID'];

		$publication = new publications();
		$publication = $publication->get($user['ab_pID']);



		$ID = (isset($_GET['ID'])) ?  $_GET['ID'] : "";
		$type = (isset($_GET['type'])) ?  $_GET['type'] : "";


		$values = array();

		$values['pID'] = $user['ab_pID'];
		$values['dID'] = "";
		$values['typeID'] = $type;
		if (!$ID) $values['userID'] = $userID;
		//userName



		if (isset($_POST['client'])) $values['client'] = $_POST['client'];
		if (isset($_POST['colourID'])) $values['colourID'] = $_POST['colourID'];
		if (isset($_POST['colourSpot'])) $values['colourSpot'] = $_POST['colourSpot'];
		if (isset($_POST['col'])) $values['col'] = $_POST['col'];
		if (isset($_POST['cm'])) $values['cm'] = $_POST['cm'];
		if (isset($values['cm']) && isset($values['col'])) $values['totalspace'] = $values['cm']* $values['col'];


		if (isset($_POST['rate'])) $values['rate'] = ($_POST['rate'])? $_POST['rate']: $_POST['rate_fld'];
		if (isset($_POST['totalCost'])) $values['totalCost'] = ($_POST['totalCost']) ? $_POST['totalCost'] : $_POST['totalShouldbe'];
		if (isset($_POST['totalShouldbe'])) $values['totalShouldbe'] = $_POST['totalShouldbe_e'];


		if (isset($_POST['discount'])) $values['discount'] = $_POST['discount'];
		if (isset($_POST['agencyDiscount'])) $values['agencyDiscount'] = $_POST['agencyDiscount'];


		if (isset($_POST['placingID']))	$values['placingID'] = $_POST['placingID'];

		if (isset($_POST['categoryID'])) $values['categoryID'] = $_POST['categoryID'];

		if (isset($_POST['InsertPO'])) $values['InsertPO'] = ($_POST['InsertPO'])? $_POST['InsertPO']: $publication['printOrder'];
		if (isset($_POST['rate'])&& $type=="2") $values['InsertRate'] = ($_POST['rate']) ? $_POST['rate'] : $publication['InsertRate'];


		if (isset($_POST['accNum'])) $values['accNum'] = $_POST['accNum'];
		if (isset($_POST['orderNum'])) $values['orderNum'] = $_POST['orderNum'];
		if (isset($_POST['keyNum'])) $values['keyNum'] = $_POST['keyNum'];
		if (isset($_POST['remark'])) $values['remark'] = $_POST['remark'];
		if (isset($_POST['remarkTypeID'])) $values['remarkTypeID'] = $_POST['remarkTypeID'];

		if (isset($_POST['marketerID'])) $values['marketerID'] = $_POST['marketerID'];



		//if (isset($_POST['accNum'])) $values['accNum'] = $_POST['accNum'];




		$a = array();
		$b = array();
		foreach ($_POST['dID'] AS $date){
			$v = $values;
			$v['dID'] = $date;

			$b[] = $v;
			$a[] = bookings::save($ID, $v);
		}


		$r = array();
		$r['ID'] = $ID;
		$r['values'] = $b;
		$r['post'] = $_POST;
		test_array($a);
		exit();



	}

}
