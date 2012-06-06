<?php

namespace models\ab;
use \F3 as F3;
use \timer as timer;
class pages {
	private $classname;
	function __construct() {

		$classname = get_class($this);
		$this->dbStructure = $classname::dbStructure();

	}
	function get($ID){
		$timer = new timer();
		$user = F3::get("user");
		$userID = $user['ID'];


		$result = F3::get("DB")->exec("
			SELECT *
			FROM global_pages
			WHERE ID = '$ID'
		"
		);


		if (count($result)) {
			$return = $result[0];
		} else {
			$return = $this->dbStructure;
		}
		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getAll($where="", $orderby=""){
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
			SELECT global_pages.*, section, section_colour
			FROM global_pages LEFT JOIN global_pages_sections ON global_pages.sectionID = global_pages_sections.ID
			$where
			$orderby
		"
		);

		$r = array();
		foreach ($result as $item){
			$item['page']=number_format($item['page'],0);
			$r[] = $item;
		}

		$return = $r;
		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function maxPages($dID,$cm=""){
		$timer = new timer();

		if (is_array($dID)){
			$date = $dID;
		} else {
			$date = new dates();
			$date = $date->get($dID);
		}



		if ($date['pages']){
			$return = $date['pages'];
		} else {
			$pID = $date['pID'];
			$dID = $date['ID'];



				if (is_numeric($cm)){
				} else {
					$stats = record_stats::stats_cm($cm);
					$cm = $stats['cm'];
				}



			$stats['loading'] = loading::getLoading($pID, $cm, $date['pages']);

			//test_array($stats);
			$return = $stats['loading']["pages"];
		}

		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}

	private static function dbStructure() {
		$table = F3::get("DB")->exec("EXPLAIN global_pages;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}

		return $result;
	}
}