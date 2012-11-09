<?php
/*
 * Date: 2011/11/16
 * Time: 11:29 AM
 */
namespace models;

use \F3 as F3;
use \Axon as Axon;
use \timer as timer;

class company {
	public $ID;
	private $dbStructure;

	function __construct() {
		$classname = get_class($this);
		$this->dbStructure = $classname::dbStructure();

	}

	public function get($ID = "") {
		$timer = new timer();
		$result = F3::get("DB")->exec("
				SELECT global_companies.*
				FROM global_companies
				WHERE global_companies.ID = '$ID'
			");
		if (count($result)) {
			$result = $result[0];
		} else {
			$result = $this->dbStructure();
		}

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $result;
	}


	private static function dbStructure() {
		$table = F3::get("DB")->exec("EXPLAIN global_companies;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}


		return $result;
	}
}
