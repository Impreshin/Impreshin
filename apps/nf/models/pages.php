<?php

namespace apps\nf\models;


use \timer as timer;

class pages {
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
			FROM global_pages
			WHERE ID = '$ID'
		");


		if (count($result)) {
			$return = $result[0];
			$return['page'] = number_format($return['page'], 0);
		} else {
			$return = $this->dbStructure;
		}
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getAll($where = "", $orderby = "global_pages.page ASC") {
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


		$timer2 = new timer();
		$result = $f3->get("DB")->exec("
			SELECT global_pages.*, section, section_colour, global_pages.nf_locked as locked
			FROM global_pages LEFT JOIN global_pages_sections ON global_pages.sectionID = global_pages_sections.ID
			$where
			$orderby
		");
		$timer2->stop("data");
		$timer3 = new timer();
		$r = array();
		foreach ($result as $item) {
			
			$item['page'] = number_format($item['page'], 0);
			$r[] = $item;
		}

		$timer3->stop("looping");
		$return = $r;
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	

	public static function dbStructure() {
		$f3 = \Base::instance();
		$table = $f3->get("DB")->exec("EXPLAIN global_pages;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}

		return $result;
	}
}