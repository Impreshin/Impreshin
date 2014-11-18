<?php

namespace apps\cm\models;
use \timer as timer;

class settings extends _ {

	public static function _read($section,$av_section="") {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");

		$settings = \apps\cm\settings::_available($user['permissions'],$av_section);
		$defaults = \apps\cm\settings::defaults();
		$settings_raw = $settings;



		
		$user_settings = $user['settings'];
		$permission = $user['permissions'];

		$return = array();

		//test_array($user_settings);
		$return = $user_settings[$section];
		//test_array($return); 

		if (isset($user_settings[$section]['col']) && count($settings["columns"])) {
			$columns = array();

			foreach ($user_settings[$section]['col'] as $col) {
				if ($col=="checkbox"){
					$columns[] = array(
						"h"=> "checkbox"
					);
				}
				if (isset($settings['columns'][$col])) {
					$columns[] = $settings['columns'][$col];
				}
			}


			$return['col'] = $columns;
			$return['count'] = count($columns);
		}
		if (isset($settings_raw['groupby'][$section])) {
			$return['groupby'] = $settings_raw['groupby'][$section];
		}
//test_array($settings['groupby'][$section]);
		
		

		if (isset($return['col'])) {
			if (isset($return['group']) && isset($settings['groupby'][$section])) {
				$gb = array();

				foreach ($settings['groupby'][$section] as $g) {
					$gb[] = $g['g'];
				}

				if (!in_array($return['group']['g'], $gb)) {
					if (isset($defaults['settings'][$section]['group']['g'])) {
						$return['group']['g'] = $defaults['settings'][$section]['group']['g'];
					}
				}


			}
			if (isset($return['order'])) {

				$gb = array();

				//test_array($settings['columns']);
				foreach ($settings['columns'] as $k => $g) {

					$gb[] = isset($g['o']) ? $g['o'] : $k;
				}


				if (!in_array($return['order']['c'], $gb)) {
					if (isset($defaults['settings'][$section]['order']['c'])) {
						$return['order']['c'] = $defaults['settings'][$section]['order']['c'];
					}
				}


			}
			/*
			//test_array($defaults);
			if (isset($return['order'])){
				if (!isset($settings['columns'][$return['order']['c']])&&isset($defaults['settings'][$section]['order']['c'])) {
					$return['order']['c'] = $defaults['settings'][$section]['order']['c'];
				}
				if (!in_array($return['order']['o'],array("ASC","DESC")) && isset($defaults['settings'][$section]['order']['o'])) {
					$return['order']['o'] = $defaults['settings'][$section]['order']['o'];
				}


			}
			*/
		}

		//	test_array($return);


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}
	public static function save($values = array(), $uID = "") {
		$timer = new timer();
		$f3 = \Base::instance();
		if (!$uID) {
			$user = $f3->get("user");
			$uID = $user['ID'];
		}


		$t = New \DB\SQL\Mapper($f3->get("DB"),"cm_users_settings");
		$t->load("uID='$uID'");

		$t->uID = $uID;
		
		$cur = ($t->settings) ? unserialize($t->settings) : array();

		$v = array_replace_recursive($cur, $values);
		if (isset($values['companies']['col'])) $v['companies']['col'] = $values['companies']['col'];
		if (isset($values['contacts']['col'])) $v['contacts']['col'] = $values['contacts']['col'];
		//test_array(array("v"=>$v,"t"=>$cur,"values"=>$values)); 

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


		$t = New \DB\SQL\Mapper($f3->get("DB"),"cm_users_settings");
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
		$table = $f3->get("DB")->exec("EXPLAIN cm_users_settings;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}
		$result["settings"] = array();

		return $result;
	}

}