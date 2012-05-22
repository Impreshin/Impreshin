<?php

namespace models\ab;
use \F3 as F3;
use \timer as timer;
class publications {
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
			FROM global_publications
			WHERE ID = '$ID'
		"
		);


		if (count($result)) {
			$return = $result[0];
		} else {
			$return = $this->dbStructure;
		}
		$timer->stop("Models - publications - get", func_get_args());
		return $return;
	}

	public static function getAll($where="", $orderby=""){
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
			SELECT DISTINCT global_publications.*, ab_users_pub.uID
			FROM global_publications INNER JOIN ab_users_pub ON global_publications.ID = ab_users_pub.pID
			$where
			$orderby
		"
		);


		$return = $result;
		$timer->stop("Models - publications - getAll", func_get_args());
		return $return;
	}


	private static function dbStructure() {
		$table = F3::get("DB")->exec("EXPLAIN global_publications;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}
		$result["heading"] = "";
		$result["publishDateDisplay"] = "";
		return $result;
	}
}