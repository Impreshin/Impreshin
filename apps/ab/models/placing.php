<?php

namespace apps\ab\models;



use \timer as timer;

class placing {
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
			FROM ab_placing
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
		$f3 = \Base::instance();
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}

		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}


		$result = $f3->get("DB")->exec("
			SELECT *, (SELECT count(ID) FROM ab_placing_sub WHERE ab_placing_sub.placingID = ab_placing.ID) AS colourCount
			FROM ab_placing
			$where
			$orderby
		");


		$return = $result;
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function save($ID, $values) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");


		$old = array();
		$lookupColumns = array();
		$a = new \DB\SQL\Mapper($f3->get("DB"),"ab_placing");
		$a->load("ID='$ID'");

		foreach ($values as $key => $value) {
			$old[$key] = isset($a->$key) ? $a->$key : "";
			if (isset($a->$key)) {

				$a->$key = $value;
			}
		}
		if (!$a->dry()) {
			$label = "Record Edited ($a->placing)";
		} else {
			$label = "Record Added (" . $values['placing'] . ')';
		}
		$a->save();

		$ID = $a->ID;


		//test_array($new_logging);


		\models\logging::_log("placing", $label, $values, $old);


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $ID;

	}

	public static function _delete($ID) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");


		$a = new \DB\SQL\Mapper($f3->get("DB"),"ab_placing");
		$a->load("ID='$ID'");

		$a->erase();

		$a->save();


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return "done";

	}


	public static function copyfrom($new_pID, $old_pID){
		$timer = new timer();
		$f3 = \Base::instance();


		$source = $f3->get("DB")->exec("
			SELECT *
			FROM ab_placing
			where pID = '$old_pID'
		"
		);
		$source_sub = $f3->get("DB")->exec("
			SELECT *
			FROM ab_placing_sub
			where pID = '$old_pID'
		"
		);
		$subs = array();
		foreach ($source_sub as $item_sub){
			$subs[$item_sub['placingID']][] = $item_sub;
		}


		//test_array($subs);

		foreach ($source as $item){
			$subs_ =isset($subs[$item['ID']])? $subs[$item['ID']]: array();
			$item['pID']=$new_pID;
			unset($item['ID']);

			$ID = self::save("",$item);
			foreach ($subs_ as $subPlacing){
				unset($subPlacing['ID']);
				$subPlacing['placingID'] = $ID;
				$subPlacing['pID'] = $new_pID;
				sub_placing::save("",$subPlacing);
			}
		}



		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return "done";
	}



	private static function dbStructure() {
		$f3 = \Base::instance();
		$table = $f3->get("DB")->exec("EXPLAIN ab_placing;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}

		return $result;
	}
}