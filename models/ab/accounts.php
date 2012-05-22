<?php

namespace models\ab;
use \F3 as F3;
use \timer as timer;
class accounts {
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
			SELECT ab_accounts.*
			FROM ab_accounts
			WHERE ab_accounts.ID = '$ID';

		"
		);


		if (count($result)) {
			$return = $result[0];
		} else {
			$return = $this->dbStructure;
		}
		$timer->stop("Models - accounts - get", func_get_args());
		return $return;
	}
	public static function getAll($where = "", $orderby = "") {
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
			SELECT ab_accounts.*, ab_accounts_status.blocked, ab_accounts_status.labelClass, ab_accounts_status.status, ab_accounts_pub.pID
			FROM (ab_accounts INNER JOIN ab_accounts_status ON ab_accounts.statusID = ab_accounts_status.ID) INNER JOIN ab_accounts_pub ON ab_accounts.ID = ab_accounts_pub.aID
			$where
			$orderby
		");


		$return = $result;
		$timer->stop("Models - accounts - getAll", func_get_args());
		return $return;
	}



	private static function dbStructure() {
		$table = F3::get("DB")->exec("EXPLAIN ab_accounts;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}

		return $result;
	}
}