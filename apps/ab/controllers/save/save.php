<?php



namespace apps\ab\controllers\save;
use \timer as timer;
use \apps\ab\models as models;
use \models\user as user;


use \apps\ab\models\bookings as bookings;
use models\dates as dates;
use \apps\ab\models\loading as loading;
use \models\publications as publications;

class save {

	function __construct() {
		$this->f3 = \base::instance();
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		if (!$userID) exit(json_encode(array("error" => $this->f3->get("system")->error("U01"))));
		$this->f3->set("json", true);
	}

	function __destruct() {


	}


	function list_settings(){
		$user = $this->f3->get("user");
		$userID = $user['ID'];

		$reset = (isset($_GET['reset'])) ? explode(",",$_GET['reset']) : array();
		$ab_defaults = \apps\ab\settings::defaults();
		$section = (isset($_GET['section'])) ? $_GET['section'] : "";



		if (in_array("columns", $reset)){
			$columns = $ab_defaults[$section]['col'];
		} else {
			$columns = (isset($_POST['columns'])) ? explode(",", $_POST['columns']) : $ab_defaults[$section]['col'];
		}

		$group = (!in_array("group", $reset)) ?(isset($_POST['group']))?$_POST['group']: $ab_defaults[$section]['group']['g'] : $ab_defaults[$section]['group']['g'];
		$order = (!in_array("order", $reset)) ?(isset($_POST['order']))?$_POST['order']: $ab_defaults[$section]['group']['o'] : $ab_defaults[$section]['group']['o'];




		$new = array();
		$new["col"] = $columns;
		$new["count"] = count($columns);
		$new["group"] = array(
			"g"=> $group,
			"o"=> $order
		);
		//$new["order"] = $order;


		$values = array();
		$values[$section]=$new;



		models\settings::save($values);



		echo json_encode(array("result"=> "1"));
		exit();
	}



	function material_status(){
		$user = $this->f3->get("user");
		$ID = (isset($_GET['ID'])) ? $_GET['ID'] : "";
		$section = "";
		$cfg = $this->f3->get("CFG");
		$cfg = $cfg['upload'];


		$values = array();
		if (isset($_POST['source'])) $values['material_source'] = $_POST['source'];
		if (isset($_POST['material_productionID'])) $values['material_productionID'] = $_POST['material_productionID'];

		if (isset($_POST['material_approved'])) $values['material_approved'] = $_POST['material_approved'];

		if (isset($_POST['material_status'])) $values['material_status'] = $_POST['material_status'];

		if (($cfg['material'] && $user['company']['ab_upload_material'] == '1' && $user['publication']['ab_upload_material'] == '1') && isset($values['material_status'])){
			if (isset($_POST['material_file_filename'])) $values['material_file_filename'] = $_POST['material_file_filename'];
			if (isset($_POST['material_file_filesize'])) $values['material_file_filesize'] = $_POST['material_file_filesize'];
			if (isset($_POST['material_file_store'])) $values['material_file_store'] = $_POST['material_file_store'];

			$section = "material";

			if (isset($values['material_file_filename'])){
				if ($values['material_status'] && $values['material_file_filename']) {
					$values['material_status'] = '1';
					$values['material_date'] = date("Y-m-d H:i:s");

				} else {
					$values['material_status'] = '0';
					$values['material_file_filename'] = "";
					$values['material_file_filesize'] = "";
					$values['material_file_store'] = "";


				}
				$values['material_approved'] = '0';
			} else {

			}





		} else {
			if (isset($_POST['material_status'])) $values['material_status'] = $_POST['material_status'];
			if (isset($values['material_status'])){
				$section = "material";
				if ($values['material_status'] == '1') {
					$values['material_date'] = date("Y-m-d H:i:s");
				} else {
					$values['material_approved'] = '0';
				}
			}
		}


		if (isset($_POST['material_approved'])){
			$section = "material_approved";
		}

		//test_array($values);

		if ($section) bookings::save($ID, $values,array("section"=> $section,"dry"=>false));
		test_array($values);
	}
	function checked_status(){
		$ID = (isset($_GET['ID'])) ? $_GET['ID'] : "";
		$section = "";

		$user = $this->f3->get("user");
		$userID = $user['ID'];


		$values = array();
		if (isset($_POST['checked'])) $values['checked'] = ($_POST['checked'])?'1':'0';



			$section = "checked";
			if ($values['checked'] == '1') {
				$values['checked_date'] = date("Y-m-d H:i:s");
				$values['checked_userID'] = $userID;
			} else {
				$values['checked'] = '0';
				$values['checked_userID'] = '';
				$values['checked_date'] = '';
				$values['checked_user'] = '';
				$values['pageID'] = null;
			}





		if ($section) bookings::save($ID, $values,array("section"=> $section,"dry"=>false));
		test_array($values);
	}
	function invoice(){
		$ID = (isset($_GET['ID'])) ? $_GET['ID'] : "";
		$section = "";

		$user = $this->f3->get("user");
		$userID = $user['ID'];


		$values = array();
		if (isset($_POST['invoiceNum'])) $values['invoiceNum'] = $_POST['invoiceNum'];


		$section = "invoice";



		if ($section) bookings::save($ID, $values, array("section"=> $section,"dry" => false));
		test_array($values);


	}
}
