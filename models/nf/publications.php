<?php

namespace models\nf;

use \F3 as F3;
use \Axon as Axon;
use \timer as timer;

class publications {
	private $classname;

	function __construct() {

		$classname = get_class($this);
		$this->dbStructure = $classname::dbStructure();

	}

	function get($ID) {
		$timer = new timer();
		$user = F3::get("user");
		$userID = $user['ID'];


		$result = F3::get("DB")->exec("
			SELECT *
			FROM global_publications
			WHERE ID = '$ID'
		");


		if (count($result)) {
			$return = $result[0];
			$return['current_date'] = $currentDate = dates::getCurrent($return['ID']);
		} else {
			$return = $this->dbStructure;
		}
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());

		return $return;
	}

	public static function getAll_user($where = "", $orderby = "") {
		$timer = new timer();
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}

		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}

		$where = str_replace("[access]", "COALESCE(global_users_company.nf,0)", $where);


		$result = F3::get("DB")->exec("
			SELECT DISTINCT global_publications.*, nf_users_pub.uID, COALESCE(global_users_company.nf,0) as access
			FROM (global_publications INNER JOIN nf_users_pub ON global_publications.ID = nf_users_pub.pID) INNER JOIN global_users_company ON (global_publications.cID = global_users_company.cID) AND (nf_users_pub.uID = global_users_company.uID)
			$where
			$orderby
		");


		$return = $result;
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getAll($where = "", $orderby = "") {
		$timer = new timer();
		$user = F3::get("user");
		$uID = $user['ID'];
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}

		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}


		$result = F3::get("DB")->exec("
			SELECT DISTINCT global_publications.* ,
				if ((SELECT count(ID) FROM nf_users_pub WHERE nf_users_pub.pID = global_publications.ID AND nf_users_pub.uID = '$uID' LIMIT 0,1)<>0,1,0) as currentUser,
				COALESCE((SELECT ID FROM global_dates WHERE global_dates.ID = global_publications.nf_currentDate), (SELECT ID FROM global_dates WHERE global_dates.pID = global_publications.ID ORDER BY publish_date DESC LIMIT 0,1)) as nf_currentDate
			FROM global_publications
			$where
			$orderby
		");


		$return = $result;
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function addUser($publications = array(), $available_publications = array()) {
		$timer = new timer();


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function save($ID, $values) {
		$user = F3::get("user");
		$timer = new timer();

		$a = new Axon("global_publications");
		$a->load("ID='$ID'");

		foreach ($values as $key => $value) {
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
		$user = F3::get("user");
		$timer = new timer();

		$a = new Axon("global_publications");
		$a->load("ID='$ID'");

		$a->erase();


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return "done";

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