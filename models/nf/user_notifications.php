<?php
/**
 * User: William
 * Date: 2012/07/03 - 2:47 PM
 */
namespace models\nf;

use \F3 as F3;
use \Axon as Axon;
use \timer as timer;

class user_notifications {

	public static function show() {
		$return = array();
		$return['footer'] = self::bar();

		return $return;
	}

	public static function bar() {
		$timer = new timer();
		$user = F3::get("user");
		$return = array();


		if (!count($return)) $return = false;
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

}
