<?php
/*
 * Date: 2011/11/16
 * Time: 11:29 AM
 */
namespace models;



use timer as timer;

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


		$cfg = $f3->get("CFG");



		if (count($result)) {
			$result = $result[0];
			
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
		$app = $f3->get("app");
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
		$where = str_replace("[access]", "(COALESCE(global_users_company." . $app . ",0)=1 AND COALESCE(global_companies." . $app . ",0)=1)", $where);



		//test_array($where);
		$sql = "SELECT DISTINCT global_companies.*, global_companies.company, global_users_company.allow_setup
				FROM global_companies INNER JOIN global_users_company ON global_companies.ID = global_users_company.cID
				$where
			$orderby
			$limit

				";

		$result = $f3->get("DB")->exec($sql);


		$return = $result;
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
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
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;

	}

	public static function save($ID, $values, $cID = "") {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");


		$a = new \DB\SQL\Mapper($f3->get("DB"), "global_companies");
		$a->load("ID='$ID'");
		$old = array();
		foreach ($values as $key => $value) {
			if (isset($a->$key)) {
				$a->$key = $value;
			}
		}
		$a->save();

		$ID = $a->ID;




		//test_array($new_logging);



		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $ID;

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
