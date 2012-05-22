<?php

namespace models\ab;
use \F3 as F3;
use \timer as timer;
class bookingTypes {
	private $classname;
	function __construct() {

		$classname = get_class($this);
		$this->dbStructure = $classname::dbStructure();

	}
	function get($ID){
		$timer = new timer();
		$user = F3::get("user");
		$userID = $user['ID'];


		$result = F3::get("DB")->exec("
			SELECT * FROM ab_bookings_types WHERE ID = '$ID';

		"
		);


		if (count($result)) {
			$return = $result[0];

		} else {
			$return = $this->dbStructure;
		}
		$timer->stop("Models - types - get", func_get_args());
		return $return;
	}
	public static function getAll($where = "", $orderby = "orderby ASC") {
		$timer = new timer();
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}

		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}




		$result = F3::get("DB")->exec("
			SELECT *
			FROM ab_bookings_types
			$where
			$orderby
		");


		$return = $result;
		$timer->stop("Models - types - getAll", func_get_args());
		return $return;
	}



	private static function dbStructure() {
		$table = F3::get("DB")->exec("EXPLAIN ab_bookings_types;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}

		return $result;
	}
}