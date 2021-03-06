<?php

namespace models;

use \timer as timer;

class publications {
	private $classname;

	function __construct() {

		$classname = get_class($this);
		$this->dbStructure = $classname::dbStructure();

	}

	function get($ID) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		$userID = $user['ID'];
		$app = $f3->get("app");
		$cfg = $f3->get("CFG");


		$result = $f3->get("DB")->exec("
			SELECT *
			FROM global_publications
			WHERE ID = '$ID'
		");


		if (count($result)) {
			$return = $result[0];
			if (in_array($app,array("ab","nf"))){
				$return['current_date'] = $currentDate = dates::getCurrent($return['ID']);
			}
			$colours = $cfg['default_colours'];
			


			if ($return['colours']){
				$colours = explode(",", $return['colours']);
			}
			$colours = implode(",",$colours);

			$return['colours'] = global_colours::getAll("ID in ($colours)");
			$return['colours_group'] = global_colours::getAll_group("", $return['colours']);

		} else {
			$return = $this->dbStructure;
		}
		//test_array($return);

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());

		return $return;
	}

	public static function getAll_user($where = "", $orderby = "") {
		$timer = new timer();
		$f3 = \Base::instance();
		$app = $f3->get("app");
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}

		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}


		$where = str_replace("[access]", "COALESCE(global_users_company.". $app.",0)", $where);

		$app_users_pub = $app."_users_pub";
		$currentDate = "";
		if (in_array($app,array("ab","nf"))){
			$currentDate = ", COALESCE((SELECT publish_date FROM global_dates WHERE global_dates.ID = ".$app."_currentDate),(SELECT publish_date FROM global_dates WHERE global_dates.pID=global_publications.ID ORDER BY publish_date DESC LIMIT 0,1)) AS currentDate, COALESCE((SELECT ID FROM global_dates WHERE global_dates.ID = ".$app."_currentDate),(SELECT ID FROM global_dates WHERE global_dates.pID=global_publications.ID ORDER BY publish_date DESC LIMIT 0,1)) AS currentDateID";
		}

		
		

		$result = $f3->get("DB")->exec("
			SELECT DISTINCT global_publications.*, $app_users_pub.uID, COALESCE(global_users_company.ab,0) AS access, global_companies.company
			
			$currentDate

			FROM ((global_publications INNER JOIN $app_users_pub ON global_publications.ID = $app_users_pub.pID) INNER JOIN global_users_company ON ($app_users_pub.uID = global_users_company.uID) AND (global_publications.cID = global_users_company.cID)) INNER JOIN global_companies ON global_publications.cID = global_companies.ID
			$where
			$orderby
		");


		$return = $result;
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getAll($where = "", $orderby = "") {
		$timer = new timer();
		$f3 = \Base::instance();
		$app = $f3->get("app");
		$user = $f3->get("user");
		$uID = $user['ID'];
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}

		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}

		$app_users_pub_sql = "";
		if ($app!="setup"){
			$app_users_pub = $app . "_users_pub";
			$app_users_pub_sql = ", if ((SELECT count($app_users_pub.ID) FROM $app_users_pub WHERE $app_users_pub.pID = global_publications.ID AND $app_users_pub.uID = '$uID' LIMIT 0,1)<>0,1,0) AS currentUser";
		}
		$currentDate = "";
		if (in_array($app,array("ab","nf"))) {
			$currentDate = ", COALESCE((SELECT publish_date FROM global_dates WHERE global_dates.ID = " . $app . "_currentDate),(SELECT publish_date FROM global_dates WHERE global_dates.pID=global_publications.ID ORDER BY publish_date DESC LIMIT 0,1)) AS currentDate, COALESCE((SELECT ID FROM global_dates WHERE global_dates.ID = " . $app . "_currentDate),(SELECT ID FROM global_dates WHERE global_dates.pID=global_publications.ID ORDER BY publish_date DESC LIMIT 0,1)) AS currentDateID";
		}
		$result = $f3->get("DB")->exec("
			SELECT DISTINCT global_publications.*, global_companies.company $currentDate $app_users_pub_sql
			FROM global_publications INNER JOIN global_companies ON global_publications.cID = global_companies.ID
			$where
			$orderby
		");


		$return = $result;
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function addUser($publications = array(), $available_publications = array()) {
		$timer = new timer();
		$f3 = \Base::instance();


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function save($ID, $values, $cID="") {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");


		$a = new \DB\SQL\Mapper($f3->get("DB"),"global_publications");
		$a->load("ID='$ID'");
		$old = array();
		foreach ($values as $key => $value) {
			$old[$key] = isset($a->$key) ? $a->$key : "";
			if (isset($a->$key)) {

				$a->$key = $value;
			}
		}
		if (!$a->dry()) {
			$label = "Record Edited ($a->publication)";
		} else {
			$label = "Record Added (" . $values['publication'] . ')';
		}
		$a->save();

		$ID = $a->ID;




		//test_array($new_logging);


		\models\logging::_log("publications", $label, $values, $old, array(), array(),$cID);

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $ID;

	}

	public static function _delete($ID) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");


		$a = new \DB\SQL\Mapper($f3->get("DB"),"global_publications");
		$a->load("ID='$ID'");

		$a->erase();


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return "done";

	}

	private static function dbStructure() {
		$f3 = \Base::instance();
		$cfg = $f3->get("CFG");
		$table = $f3->get("DB")->exec("EXPLAIN global_publications;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}
		$result["heading"] = "";
		$result["publishDateDisplay"] = "";
		$result["colours"] = global_colours::getAll("ID in (".implode(",", $cfg['default_colours'] ).")");
		return $result;
	}
}