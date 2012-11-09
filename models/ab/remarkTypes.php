<?php

namespace models\ab;

use \F3 as F3;
use \timer as timer;

class remarkTypes {
	private $classname;

	function __construct() {


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
			SELECT *
			FROM ab_remark_types
			$where
			$orderby
		");


		$return = $result;
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}


}