<?php

namespace models\ab;
use \F3 as F3;
use \Axon as Axon;
use \timer as timer;
class marketers_targets {
	private $classname;
	function __construct() {

		$classname = get_class($this);
		$this->dbStructure = $classname::dbStructure();

	}
	public static function _current($mID,$pID){
		$timer = new timer();
		$result = array();



			$ID = $mID;
		if ($ID){


			$month = date("m");
			$year = date("Y");


			$result = F3::get("DB")->exec("
				SELECT target FROM ab_marketers_targets WHERE ab_marketers_targets.mID = '$ID' AND ab_marketers_targets.pID ='$pID' AND monthin='$month' AND yearin='$year'
			");
			$done = 0;
			$b = bookings::getAll("month(global_dates.publish_date)='$month' and year(global_dates.publish_date)='$year' AND ab_bookings.pID='$pID' AND marketerID='$ID'");

			foreach($b as $t){
				$done = $done + $t['totalCost'];
			}
			//test_array($b);


			$return['targets']['records'] = count($b);
			$return['targets']['done'] = currency($done);
			if (count($result)) {
				$return['targets']['target'] = currency($result[0]['target']);
				$return['targets']['percent'] = number_format((($done / $result[0]['target'])*100),2);
			} else {


			}

	} else {
			$return = "";
		}





		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}
	public static function getAll($where = "", $orderby = "", $limit = "") {
		$timer = new timer();


		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}

		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}
		if ($limit) {
			$limit = " LIMIT " . $limit;
		}





		$result = F3::get("DB")->exec("
			SELECT DISTINCT ab_marketers_targets.*, lpad(ab_marketers_targets.monthin,2,'00') as monthin, STR_TO_DATE(concat('01,',monthin,',',yearin),'%d,%m,%Y') as m
			FROM ab_marketers_targets
			$where
			$orderby
		");



		$return = $result;
		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}
	public static function save($ID, $values) {
		$user = F3::get("user");
		$timer = new timer();

		$a = new Axon("ab_marketers_targets");
		$a->load("ID='$ID'");

		foreach ($values as $key=> $value) {
			$a->$key = $value;
		}

		$a->save();

		if (!$a->ID) {
			$ID = $a->_id;
		}




		$timer->stop(array("Models"=> array("Class" => __CLASS__,"Method"=> __FUNCTION__)), func_get_args());
		return $ID;

	}

	public static function _delete($ID) {
		$user = F3::get("user");
		$timer = new timer();

		$a = new Axon("ab_marketers_targets");
		$a->load("ID='$ID'");

		$a->erase();






		$timer->stop(array("Models"=> array("Class" => __CLASS__,"Method"=> __FUNCTION__)), func_get_args());
		return "done";

	}



	private static function dbStructure() {
		$table = F3::get("DB")->exec("EXPLAIN ab_marketers;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}

		return $result;
	}
}