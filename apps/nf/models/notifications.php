<?php
/**
 * User: William
 * Date: 2012/07/03 - 2:47 PM
 */
namespace apps\nf\models;



use \timer as timer;

class notifications {
	public static function show() {
		$return = array();
		$return['footer'] = self::bar();

		return $return;
	}

	public static function bar() {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		$return = $records = array();



		if (!count($return)) $return = false;
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

}