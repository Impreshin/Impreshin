<?php

namespace models\ab;
use \F3 as F3;
use \timer as timer;
class dates {
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
			SELECT *
			FROM global_dates
			WHERE ID = '$ID'
		"
		);


		if (count($result)) {
			$return = $result[0];
			$return['publish_date_display'] = date("d F Y", strtotime($return['publish_date']));

		} else {
			$return = $this->dbStructure;
		}
		$timer->stop("Models - bookings - get", func_get_args());
		return $return;
	}
	public static function getCurrent($pID) {
		$timer = new timer();

		$result = F3::get("DB")->exec("
			SELECT *
			FROM global_dates
			WHERE pID = '$pID' AND ab_current = '1'
			ORDER BY publish_date DESC
		");

		if (count($result)) {
			$return = $result[0];
			$return['publish_date_display'] = date("d F Y", strtotime($return['publish_date']));

		} else {
			$return = F3::get("system")->error("D02");
		}

		$timer->stop("Models - dates - getCurrent", func_get_args());
		return $return;
	}

	public static function getAll($where="",$orderby="publish_date DESC",$limit="") {
		$timer = new timer();
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}

		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}
		if ($limit) {
			$limit = " LIMIT " . $limit;
		}


		$result = F3::get("DB")->exec("
			SELECT *
			FROM global_dates
			$where
			$orderby
			$limit
		");

		$a = array();
		foreach($result as $record){
			$record['publish_date_display'] = date("d F Y", strtotime($record['publish_date']));
			$a[] = $record;
		}
		$return = $a;
		$timer->stop("Models - dates - getAll", func_get_args());
		return $return;
	}




	private static function dbStructure() {
		$table = F3::get("DB")->exec("EXPLAIN global_dates;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}
		$result["heading"] = "";
		$result["publishDateDisplay"] = "";
		return $result;
	}
}