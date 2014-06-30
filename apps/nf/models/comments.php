<?php

namespace apps\nf\models;



use \timer as timer;

class comments {
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
			FROM nf_comments
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

	public static function getAll($where = "", $orderby = "", $pID="") {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}

		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}


		$result = $f3->get("DB")->exec("
			SELECT nf_comments.*, global_users.fullName
			FROM nf_comments INNER JOIN global_users ON nf_comments.uID = global_users.ID
			$where
			$orderby
		");


		$return = $result;
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function display($data) {
		$return = array();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		$rows = array();

		foreach ($data as $item) {
			if (isset($item['datein']) && $item['datein']) $item['datein'] = datetime($item['datein'],'',$user['company']['timezone']);


			$item['children'] = array();
			$rows[$item['ID']] = $item;

		
		}

		



		foreach ($rows as $k => &$v) {
			if ($v['parentID'] == $v['ID']) continue;
			if (isset($rows[$v['parentID']])) {
				$rows[$v['parentID']]['children'][] = & $v;
			}
		}

		foreach ($rows as $item) {
			if ($item['parentID']) unset($rows[$item['ID']]);
		}

		//	array_splice($rows, 2);
		//test_array($rows);


		foreach ($rows as $item) {



			$return[] = $item;


		}
		
		



		return $return;
	}

	public static function save($ID, $values) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");


		$old = array();


		$a = new \DB\SQL\Mapper($f3->get("DB"),"nf_comments");
		$a->load("ID='$ID'");

		foreach ($values as $key => $value) {
			$old[$key] = isset($a->$key) ? $a->$key : "";
			if (isset($a->$key)) {

				$a->$key = $value;
			}

		}

		if (!$a->dry()) {
			$label = "Comment Edited";
		} else {
			$label = "Comment Added";
		}

		$a->save();


		$ID = $a->ID;

		

		\models\logging::_log("nf_comments", $label, $values, $old);


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $ID;

	}

	public static function _delete($ID) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");


		$a = new \DB\SQL\Mapper($f3->get("DB"),"nf_comments");
		$a->load("ID='$ID'");

		$a->erase();

		$a->save();


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return "done";

	}


	private static function dbStructure() {
		$f3 = \Base::instance();
		$table = $f3->get("DB")->exec("EXPLAIN nf_comments;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}

		return $result;
	}
}