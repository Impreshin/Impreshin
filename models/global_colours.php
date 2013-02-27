<?php
/**
 * User: William
 * Date: 2013/02/18 - 12:46 PM
 */
namespace models;
use \timer as timer;
class global_colours {
	public static function getAll($where="", $orderby="orderby ASC"){
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

		$result = $f3->get("DB")->exec("SELECT * FROM system_publishing_colours $where $orderby");
		$return = $result;

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}
	public static function getAll_group($where="",$colours=""){
		$timer = new timer();
		$f3 = \Base::instance();
		$app = $f3->get("app");

		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}


		$result = $f3->get("DB")->exec("SELECT * FROM system_publishing_colours_groups $where");


		if (!is_array($colours))$colours = self::getAll();

		$t=  array();
		foreach ($colours as $colour){
			$t[$colour['ID']] = $colour;
		}

		$n = array();
		foreach ($result as $record){
			$record['colour_string'] = $record['colours'];
			$record['colours'] = array();
			foreach (explode(",",$record['colour_string']) as $c){
				if (isset($t[$c])) $record['colours'][] = $t[$c];
			}
			if (count($record['colours'])){
				$n[] = $record;
			}

		}

		$return = $n;



		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

}
