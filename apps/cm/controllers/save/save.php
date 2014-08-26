<?php



namespace apps\cm\controllers\save;
use \timer as timer;
use \apps\cm\models as models;
use \models\user as user;


use models\dates as dates;
use \models\publications as publications;

class save {

	function __construct() {
		$this->f3 = \base::instance();
		$this->f3->set("json", true);
	}

	function __destruct() {


	}


	function list_settings(){
		$user = $this->f3->get("user");
		$userID = $user['ID'];

		$reset = (isset($_GET['reset'])) ? explode(",",$_GET['reset']) : array();
		$app_defaults = \apps\cm\settings::defaults();
		
		
		
		$section = (isset($_GET['section'])) ? $_GET['section'] : "";

		

		if (in_array("columns", $reset)){
			$columns = $app_defaults[$section]['col'];
		} else {
			$columns = (isset($_POST['columns'])) ? explode(",", $_POST['columns']) : $app_defaults[$section]['col'];
		}

		$group = (!in_array("group", $reset)) ?(isset($_POST['group']))?$_POST['group']: $app_defaults[$section]['group']['g'] : $app_defaults[$section]['group']['g'];
		$order = (!in_array("order", $reset)) ?(isset($_POST['order']))?$_POST['order']: $app_defaults[$section]['group']['o'] : $app_defaults[$section]['group']['o'];




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




}
