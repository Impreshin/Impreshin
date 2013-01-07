<?php

namespace models\ab;

use \F3 as F3;
use \Axon as Axon;
use \timer as timer;

class user_settings extends \models\user {

	function _read($ID) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		$userID = $user['ID'];


		$result = $f3->get("DB")->exec("
			SELECT *
			FROM ab_users_settings
			WHERE uID = '$ID';

		");


		if (count($result)) {
			$return = $result[0];
		} else {
			$return = $this->settings_dbStructure();
		}
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function save_setting($values = array(), $uID = "") {
		$timer = new timer();
		$f3 = \Base::instance();
		if (!$uID) {
			$user = $f3->get("user");
			$uID = $user['ID'];
		}


		$t = New \DB\SQL\Mapper($f3->get("DB"),"ab_users_settings");
		$t->load("uID='$uID'");

		$t->uID = $uID;

		$v = array_replace_recursive(($t->settings) ? unserialize($t->settings) : array(), $values);
		if (isset($values['production']['col'])) $v['production']['col'] = $values['production']['col'];
		if (isset($values['provisional']['col'])) $v['provisional']['col'] = $values['provisional']['col'];
		if (isset($values['search']['col'])) $v['search']['col'] = $values['search']['col'];
		if (isset($values['deleted']['col'])) $v['deleted']['col'] = $values['deleted']['col'];


		if (count($values)) $t->settings = serialize($v);

		$t->save();


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return "done";
	}

	public static function save_config($values = array(), $uID = "") {
		$timer = new timer();
		$f3 = \Base::instance();
		if (!$uID) {
			$user = $f3->get("user");
			$uID = $user['ID'];
		}


		$t = New \DB\SQL\Mapper($f3->get("DB"),"ab_users_settings");
		$t->load("uID='$uID'");

		$t->uID = $uID;

		foreach ($values as $key => $value) {
			$t->$key = $value;
		}


		$t->save();


		$timer->stop(array(
		                  "Models" => array(
			                  "Class"  => __CLASS__,
			                  "Method" => __FUNCTION__
		                  )
		             ), func_get_args());
		return $uID;
	}

	private static function settings_dbStructure() {
		$f3 = \Base::instance();
		$table = $f3->get("DB")->exec("EXPLAIN ab_users_settings;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}
		$result["settings"] = array();

		return $result;
	}

}