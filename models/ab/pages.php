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
			$return['page'] = number_format($return['page'], 0);
		} else {
			$return = $this->dbStructure;
		}
		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getAll($where="", $orderby=""){
		$user = F3::get("user");
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
			SELECT global_pages.*, section, section_colour,  COALESCE((SELECT SUM(totalspace) FROM ab_bookings WHERE pageID = global_pages.ID),0) as cm
			FROM global_pages LEFT JOIN global_pages_sections ON global_pages.sectionID = global_pages_sections.ID
			$where
			$orderby
		"
		);
		$pageSize = $user['publication']['columnsav'] * $user['publication']['cmav'];
		$r = array();
		foreach ($result as $item){
			$percent = ($item['cm'])?($item['cm']/$pageSize)*100:0;
			$item['page']=number_format($item['page'],0);
			$item['percent']=number_format($percent,2);
			$r[] = $item;
		}

		$return = $r;
		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function maxPages($dID,$cm=0){
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


			$stats['loading'] = loading::getLoading($pID, $cm, $date['pages']);


			$return = $stats['loading']["pages"];
		}

		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function dbStructure() {
		$table = F3::get("DB")->exec("EXPLAIN global_pages;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}

		return $result;
	}
}