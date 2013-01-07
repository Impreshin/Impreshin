<?php

namespace models\nf;

use \F3 as F3;
use \Axon as Axon;
use \timer as timer;

class categories {
	private $classname;

	function __construct() {

		$classname = get_class($this);
		$this->dbStructure = $classname::dbStructure();

	}

	function get($ID) {
		$timer = new timer();
		$user = $f3->get("user");
		$userID = $user['ID'];


		$result = $f3->get("DB")->exec("
			SELECT *
			FROM nf_categories
			WHERE ID = '$ID';

		");


		if (count($result)) {
			$return = $result[0];
		} else {
			$return = $this->dbStructure;
		}
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getAll($where = "", $orderby = "") {
		$timer = new timer();
		$user = $f3->get("user");
		$pID = $user['publication']['ID'];
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
			FROM nf_categories
			$where
			$orderby
		");


		$return = $result;
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function save($ID, $values) {
		$user = $f3->get("user");
		$timer = new timer();

		$old = array();


		$a = new \DB\SQL\Mapper($f3->get("DB"),"nf_categories");
		$a->load("ID='$ID'");

		foreach ($values as $key => $value) {
			$old[$key] = $a->$key;
			$a->$key = $value;
		}

		$a->save();

		if (!$a->ID) {
			$ID = $a->_id;
		}




		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $ID;

	}

	public static function _delete($ID) {
		$user = $f3->get("user");
		$timer = new timer();

		$a = new \DB\SQL\Mapper($f3->get("DB"),"nf_categories");
		$a->load("ID='$ID'");

		$a->erase();

		$a->save();


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return "done";

	}


	private static function dbStructure() {
		$table = $f3->get("DB")->exec("EXPLAIN nf_categories;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}

		return $result;
	}
}