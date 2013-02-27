<?php



namespace controllers\nf\save;
use \F3 as F3;
use \timer as timer;
use models\nf as models;
use models\user as user;

class save {

	function __construct() {

		$user = $this->f3->get("user");
		$userID = $user['ID'];
		if (!$userID) exit(json_encode(array("error" => $this->f3->get("system")->error("U01"))));
	}

	function __destruct() {


	}


	function list_settings(){
		$user = $this->f3->get("user");
		$userID = $user['ID'];

		$reset = (isset($_GET['reset'])) ? explode(",",$_GET['reset']) : array();
		$ab_defaults = $this->f3->get("defaults");
		$ab_defaults = $ab_defaults['settings'];
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



		models\user_settings::save_setting($values);



		echo json_encode(array("result"=> "1"));
		exit();
	}


}
