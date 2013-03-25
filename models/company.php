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
		$f3 = \Base::instance();

		$sql = "
				SELECT global_companies.*
				FROM global_companies
				WHERE global_companies.ID = '$ID'
			";
		$result = $f3->get("DB")->exec($sql);


		$cfg = $f3->get("cfg");



		if (count($result)) {
			$result = $result[0];
			if ($cfg['online']) {
				$packagesDB = new \DB\SQL('mysql:host=' . $cfg['package']['host'] . ';dbname=' . $cfg['package']['database'] . '', $cfg['package']['username'], $cfg['package']['password']);
				$packages = $packagesDB->exec("SELECT * FROM packages WHERE ID = '". $result['packageID']."'");
				if (count($packages)) {
					$packages = $packages[0];
					unset($packages['ID']);


					$result = array_merge((array)$result, (array)$packages);

				}

		}
		} else {
			$result = $this->dbStructure();
		}

		//test_array($result);
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $result;
	}

	public static function getAll_user($where = "", $orderby = "", $limit = "") {
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

		if ($limit) {
			$limit = str_replace("LIMIT", "", $limit);
			$limit = " LIMIT " . $limit;

		}

		$sql = "SELECT DISTINCT global_companies.*, global_companies.company, global_users_company.allow_setup
				FROM global_companies INNER JOIN global_users_company ON global_companies.ID = global_users_company.cID
				$where
			$orderby
			$limit

				";

			$result = $f3->get("DB")->exec($sql);


		$return = $result;
		$timer->stop(array( "Models" => array( "Class"  => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;

	}
	public static function getAll($where = "", $orderby = "company ASC", $limit = "") {
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

		if ($limit) {
			$limit = str_replace("LIMIT", "", $limit);
			$limit = " LIMIT " . $limit;

		}

		$sql = "SELECT global_companies.*
		FROM global_companies
				$where
			$orderby
			$limit

				";

			$result = $f3->get("DB")->exec($sql);


		$return = $result;
		$timer->stop(array( "Models" => array( "Class"  => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;

	}


	private static function dbStructure() {
		$f3 = \Base::instance();
		$table = $f3->get("DB")->exec("EXPLAIN global_companies;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}


		return $result;
	}
}
