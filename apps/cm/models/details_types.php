<?php

namespace apps\cm\models;
use \timer as timer;

class details_types {
	private $classname;

	function __construct() {
		$classname = get_class($this);
		$this->dbStructure = $classname::dbStructure();
	}



	

	

	public static function getAll($where = "",$orderby="",$limit="") {
		$f3 = \Base::instance();
		$timer = new timer();
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}

		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}


		$result = $f3->get("DB")->exec("
			SELECT *
			FROM cm_details_types
			$where
			$orderby
		");


		
		$return = $result;

		//test_array($return); 
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());

		return $return;
	}

	
	public static function _delete($ID = "", $reason = "") {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		$userID = $user['ID'];
		$a = new \DB\SQL\Mapper($f3->get("DB"), "cm_details_types");
		$a->load("ID='$ID'");
		
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args()
		);

		return "deleted";
	}



	public static function save($ID, $values) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");


		$old = array();
		$lookupColumns = array();

		$a = new \DB\SQL\Mapper($f3->get("DB"),"cm_details_types");
		$a->load("ID='$ID'");

		foreach ($values as $key => $value) {
			$old[$key] = isset($a->$key) ? $a->$key : "";
			if (isset($a->$key)) {

				$a->$key = $value;
			}
		}
		if (!$a->dry()) {
			$label = "Record Edited ($a->section)";
		} else {
			$label = "Record Added (" . $values['section'] . ')';
		}
		$a->save();

		$ID = $a->ID;


		//test_array($new_logging);



		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $ID;

	}

	
	public static function dbStructure() {
		$f3 = \Base::instance();
		$table = $f3->get("DB")->exec("EXPLAIN cm_details_types;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}
		

		return $result;
	}
}