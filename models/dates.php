<?php

namespace models;

use \timer as timer;

class dates {
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


		$result = $f3->get("DB")->exec("
			SELECT *
			FROM global_dates
			WHERE ID = '$ID'
		");


		if (count($result)) {
			$return = $result[0];
			$return['publish_date_display'] = date("d F Y", strtotime($return['publish_date']));

		} else {
			$return = $this->dbStructure;
		}
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getCurrent($pID) {
		$timer = new timer();
		$f3 = \Base::instance();
		$app = $f3->get("app");
		$column = "$app" . "_currentDate";
		$result = $f3->get("DB")->exec("
			SELECT global_dates.*
			FROM global_publications INNER JOIN global_dates ON global_publications.$column = global_dates.ID
			WHERE pID = '$pID'
		");

		if (!count($result)) {

			$result = $f3->get("DB")->exec("
			SELECT global_dates.*
			FROM global_dates
			WHERE pID = '$pID'
			ORDER BY publish_date DESC
			LIMIT 0,1
		");
		}

		if (count($result)) {
			$return = $result[0];
			$return['publish_date_display'] = date("d F Y", strtotime($return['publish_date']));

		} else {
			$return = $f3->get("system")->error("D01");
		}

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getAll($where = "", $orderby = "publish_date DESC", $limit = "") {
		$timer = new timer();
		$f3 = \Base::instance();
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}

		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}
		if ($limit) {
			$limit = str_replace("LIMIT", "", $limit);
			$limit = " LIMIT " . $limit;

		}

		$app = $f3->get("app");
		if (in_array($app,array("ab","nf"))){
			$column = "$app" . "_currentDate";
			$result = $f3->get("DB")->exec("
			SELECT global_dates.*, if ($column,1,0) AS current
			FROM global_dates LEFT JOIN global_publications ON global_dates.ID = global_publications.$column
			$where
			$orderby
			$limit
		");
		} else {
			$result = $f3->get("DB")->exec("
			SELECT global_dates.*
			FROM global_dates
			$where
			$orderby
			$limit
		");
		}
		
		

		$a = array();
		foreach ($result as $record) {
			$record['publish_date_display'] = date("d F Y", strtotime($record['publish_date']));
			$a[] = $record;
		}
		$return = $a;
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getAll_count($where = "") {
		$timer = new timer();
		$f3 = \Base::instance();
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}


		$result = $f3->get("DB")->exec("
			SELECT count(ID) AS count
			FROM global_dates
			$where
		");

		if (count($result)) {
			$return = $result[0]['count'];

		} else {
			$return = 0;
		}

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function save($ID, $values) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");

		$old = array();

		if (!isset($values["pID"]) || $values["pID"] == "") $values["pID"] = $user['publication']['ID'];


		$a = new \DB\SQL\Mapper($f3->get("DB"),"global_dates");
		$a->load("ID='$ID'");

	//	test_array($values); 
		foreach ($values as $key => $value) {
			$old[$key] = isset($a->$key) ? $a->$key : "";
			if (isset($a->$key)) {
				$a->$key = $value;
			}
		}

		if (!$a->ID) {
			$label = "Record Added (" . $values['publish_date'] . ')';
		} else {
			$label = "Record Edited ($a->publish_date)";
		}
		$a->save();

		$ID = $a->ID;


		$app = $f3->get("app");

		//test_array(array($a->ID,$ID));
		if (isset($values['current']) && $values['current'] ) {
			$b = new \DB\SQL\Mapper($f3->get("DB"),"global_publications");
			$b->load("ID='" . $values["pID"] . "'");
			$column = $app . "_currentDate";
			$b->$column = $ID;
			
			//test_array($b->$column); 
			if (!$b->dry()) $b->save();
		}


	

		\models\logging::_log("dates", $label, $values, $old);


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $ID;

	}

	public static function _delete($ID) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");



		$result = $f3->get("DB")->exec("
			DELETE FROM global_dates WHERE ID = '$ID'");

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return "done";
	}


	private static function dbStructure() {
		$f3 = \Base::instance();
		$table = $f3->get("DB")->exec("EXPLAIN global_dates;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}
		$result["heading"] = "";
		$result["publishDateDisplay"] = "";
		return $result;
	}
}