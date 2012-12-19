<?php

namespace models\nf;

use \F3 as F3;
use \Axon as Axon;
use \timer as timer;

class stages {
	private $classname;

	function __construct() {

		$classname = get_class($this);
		$this->dbStructure = $classname::dbStructure();

	}

	function get($ID) {
		$timer = new timer();
		$user = F3::get("user");
		$userID = $user['ID'];


		//test_array($currentDate);

		$result = F3::get("DB")->exec("
			SELECT * FROM nf_stages	WHERE nf_stages.ID = '$ID';

		");


		if (count($result)) {
			$return = ($result[0]);
		} else {
			$return = $this->dbStructure;
		}
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getCount($where = "") {
		$timer = new timer();
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}


		$return = F3::get("DB")->exec("
			SELECT count(nf_stages.ID) as records
			FROM nf_stages
			$where
		");
		if (count($return)) {
			$return = $return[0]['records'];
		}

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getSelect($select, $where = "", $orderby, $groupby = "") {
		/*
				return array(
					"select"=>$select,
					"where"=>$where,
					"orderby"=>$orderby,
					"group"=>$groupby
				);
		*/
		$timer = new timer();
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}

		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}
		if ($groupby) {
			$groupby = " GROUP BY " . $groupby;
		}


		$return = F3::get("DB")->exec("
			SELECT $select
			FROM nf_stages


			$where
			$groupby
			$orderby
		");


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getAll($where = "", $orderby = "orderby ASC", $limit = "") {
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
			SELECT nf_stages.*

			FROM nf_stages
			$where
			$orderby
			$limit
		");


		$return = $result;

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}


	public static function _delete($ID = "", $reason = "") {
		$timer = new timer();

		$user = F3::get("user");
		$userID = $user['ID'];


		$a = new Axon("nf_stages");
		$a->load("ID='$ID'");

		$a->erase();


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return "deleted";
	}

	public static function save($ID = "", $values = array()) {
		$timer = new timer();


		$a = new Axon("nf_stages");
		$a->load("ID='$ID'");
		foreach ($values as $key => $value) {
			$cur = $a->$key;
			$a->$key = $value;
		}

		$a->save();


		if (!$ID) {
			$label = "Record Added";
			$ID = $a->_id;
		} else {
			$label = "Record Edited";
			$ID = $a->ID;
		}


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $ID;
	}


	public static function dbStructure() {
		$table = F3::get("DB")->exec("EXPLAIN nf_stages;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}

		return $result;
	}
}