<?php

namespace models\nf;

use \F3 as F3;
use \timer as timer;

class user_permissions {
	function __construct() {


	}

	public static function permissions() {
		$timer = new timer();
		$return = array();
		$return['p'] = array(

			"administration" => array(
				"application" => array(),
				"system"      => array(
					"dates"        => array(
						"page" => 0
					),
					"users"        => array(
						"page" => 0
					),
					"publications" => array(
						"page" => 0
					)
				)
			)

		);


		$return['d'] = array();


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}


	public static function _read($user_permissions) {


		$timer = new timer();
		$permissions = self::permissions();
		$permissions = $permissions['p'];


		if (count($user_permissions)) {
			$user_permissions = @unserialize($user_permissions);
			$user_permissions = array_replace_recursive((array)$permissions, (array)($user_permissions) ? $user_permissions : array());
		} else {
			$user_permissions = $permissions;
		}


		$return = array();

		//test_array($user_settings);
		$return = $user_permissions;


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function write($uID, $cID, $values) {
		$timer = new timer();
		$a = new \DB\SQL\Mapper($f3->get("DB"),"global_users_company");
		$a->load("uID='$uID' AND cID = '$cID'");
		$a->nf_permissions = serialize($values);

		if (!$a->dry()) $a->save();


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return "done";
	}

}