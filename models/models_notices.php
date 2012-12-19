<?php
/*
 * Date: 2011/11/16
 * Time: 11:29 AM
 */
namespace models;

use \F3 as F3;
use \Axon as Axon;
use \timer as timer;

class notices {
	public $ID;
	private $dbStructure;

	function __construct() {

		$classname = get_class($this);
		$this->dbStructure = $classname::dbStructure();

	}


	public static function getAll($where = "", $orderby = "datein DESC") {
		$timer = new timer();
		$return = array();
		$return['error'] = "";


		if ($where) {
			$where = " WHERE " . $where;
		}

		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}


		$result = F3::get("DB")->sql("
			SELECT *
			FROM mp_notices
			$where
			$orderby
		");

		$return['data'] = $result;


		$return['recordsCount'] = count($result);
		$timer->stop("Models - notices - getAll", func_get_args());
		return $return;
	}

	public static function seen() {
		$timer = new timer();
		$return = array();
		$return['error'] = "";
		$user = F3::get("user");
		$userID = $user['ID'];

		$result = F3::get("DB")->sql("SELECT nID FROM mp_user_seen_notice WHERE uID = '$userID'");

		$return['data'] = $result;


		$return['recordsCount'] = count($result);
		$timer->stop("Models - notices - seen", func_get_args());
		return $return;
	}

	public static function show($where = "", $orderby = "datein DESC") {
		$timer = new timer();
		$return = array();
		$return['error'] = "";


		if ($where) {
			$where = " WHERE " . $where;
		}

		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}


		$result = F3::get("DB")->sql("
			SELECT *
			FROM mp_notices
			$where
			$orderby
		");

		$return['data'] = $result;


		$return['recordsCount'] = count($result);
		$timer->stop("Models - notices - show", func_get_args());
		return $return;
	}


	private static function dbStructure() {
		$table = F3::get("DB")->sql("EXPLAIN mp_notices;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}
		return $result;
	}
}
