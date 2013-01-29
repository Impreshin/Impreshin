<?php

namespace models\ab;

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
		$f3 = \Base::instance();
		$user = $f3->get("user");
		$userID = $user['ID'];


		$result = $f3->get("DB")->exec("
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
		$f3 = \Base::instance();
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}

		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}


		$where = str_replace("[access]", "COALESCE(global_users_company.ab,0)", $where);


		$result = $f3->get("DB")->exec("
			SELECT DISTINCT global_publications.*, ab_users_pub.uID, COALESCE(global_users_company.ab,0) AS access
			FROM (global_publications INNER JOIN ab_users_pub ON global_publications.ID = ab_users_pub.pID) INNER JOIN global_users_company ON (global_publications.cID = global_users_company.cID) AND (ab_users_pub.uID = global_users_company.uID)
			$where
			$orderby
		");


		$return = $result;
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getAll($where = "", $orderby = "") {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		$uID = $user['ID'];
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}

		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}


		$result = $f3->get("DB")->exec("
			SELECT DISTINCT global_publications.* , if ((SELECT count(ID) FROM ab_users_pub WHERE ab_users_pub.pID = global_publications.ID AND ab_users_pub.uID = '$uID' LIMIT 0,1)<>0,1,0) AS currentUser
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
		$f3 = \Base::instance();


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function save($ID, $values) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");


		$a = new \DB\SQL\Mapper($f3->get("DB"),"global_publications");
		$a->load("ID='$ID'");
		$old = array();
		foreach ($values as $key => $value) {
			if (isset($a->$key)) {
				$old[$key] = isset($a->$key) ? $a->$key : "";
				$a->$key = $value;
			}
		}
		if (!$a->dry()) {
			$label = "Record Edited ($a->publication)";
		} else {
			$label = "Record Added (" . $values['publication'] . ')';
		}
		$a->save();

		$ID = $a->ID;




		//test_array($new_logging);


		\models\logging::_log("publications", $label, $values, $old);

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $ID;

	}

	public static function _delete($ID) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");


		$a = new \DB\SQL\Mapper($f3->get("DB"),"global_publications");
		$a->load("ID='$ID'");

		$a->erase();


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return "done";

	}

	private static function dbStructure() {
		$f3 = \Base::instance();
		$table = $f3->get("DB")->exec("EXPLAIN global_publications;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}
		$result["heading"] = "";
		$result["publishDateDisplay"] = "";
		return $result;
	}
}