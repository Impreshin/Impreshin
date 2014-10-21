<?php

namespace apps\cm\models;
use \timer as timer;

class companies_interactions {
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
			FROM cm_companies_interactions
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

	

	

	public static function getAll($where = "",$orderby="",$limit="") {
		$f3 = \Base::instance();
		$timer = new timer();
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}

		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}


		$result = $f3->get("DB")->exec("
			SELECT cm_companies_interactions.*, global_users.fullName
			FROM cm_companies_interactions LEFT JOIN global_users ON cm_companies_interactions.uID = global_users.ID
			$where
			$orderby
		");

		$app_settings = \apps\cm\settings::_available();
		$types = $app_settings['interaction_types_icons'];
		//test_array($app_settings); 
		
		$n = array();
		foreach ($result as $item){
			$item['icon'] = isset($types[$item['typeID']])?$types[$item['typeID']]:"";
			$n[] = $item;
		}
		

		
		$return = $n;

		//test_array($return); 
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());

		return $return;
	}

	
	public static function _delete($ID = "", $reason = "") {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		$userID = $user['ID'];
		$a = new \DB\SQL\Mapper($f3->get("DB"), "cm_companies_interactions");
		$a->load("ID='$ID'");
		$a->erase();

		$a->save();
		
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args()
		);

		return "deleted";
	}



	public static function save($ID, $values) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");


		$old = array();
		$lookupColumns = array();

		$a = new \DB\SQL\Mapper($f3->get("DB"),"cm_companies_interactions");
		$a->load("ID='$ID'");

		foreach ($values as $key => $value) {
			$old[$key] = isset($a->$key) ? $a->$key : "";
			if (isset($a->$key)) {

				$a->$key = $value;
			}
		}
		if (!$a->dry()) {
			$label = "Record Edited ($a->datein)";
		} else {
			$label = "Record Added (" . date("Y-m-d H:i:s") . ')';
		}
		$a->save();

		$ID = $a->ID;


		//test_array($new_logging);



		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $ID;

	}

	
	public static function dbStructure() {
		$f3 = \Base::instance();
		$table = $f3->get("DB")->exec("EXPLAIN cm_companies_interactions;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}
		

		return $result;
	}
}