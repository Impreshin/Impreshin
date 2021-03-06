<?php

namespace apps\nf\models;
use \timer as timer;

class settings extends _ {

	public static function _read($section) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");

		$settings = \apps\nf\settings::_available($user['permissions']);
		$defaults = \apps\nf\settings::defaults();
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


		$t = New \DB\SQL\Mapper($f3->get("DB"),"nf_users_settings");
		$t->load("uID='$uID'");

		$t->uID = $uID;

		$v = array_replace_recursive(($t->settings) ? unserialize($t->settings) : array(), $values);
		if (isset($values['production']['col'])) $v['production']['col'] = $values['production']['col'];
		if (isset($values['newsbook']['col'])) $v['newsbook']['col'] = $values['newsbook']['col'];
		if (isset($values['provisional']['col'])) $v['provisional']['col'] = $values['provisional']['col'];
		if (isset($values['search']['col'])) $v['search']['col'] = $values['search']['col'];
		if (isset($values['deleted']['col'])) $v['deleted']['col'] = $values['deleted']['col'];
		if (isset($values['reports_author_newsbook']['col'])) $v['reports_author_newsbook']['col'] = $values['reports_author_newsbook']['col'];
		if (isset($values['reports_author_submitted']['col'])) $v['reports_author_submitted']['col'] = $values['reports_author_submitted']['col'];
		if (isset($values['records_newsbook']['col'])) $v['records_newsbook']['col'] = $values['records_newsbook']['col'];
		if (isset($values['reports_publication_figures']['col'])) $v['reports_publication_figures']['col'] = $values['reports_publication_figures']['col'];
		if (isset($values['reports_category_figures']['col'])) $v['reports_category_figures']['col'] = $values['reports_category_figures']['col'];

		//test_array($v); 

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


		$t = New \DB\SQL\Mapper($f3->get("DB"),"nf_users_settings");
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
		$table = $f3->get("DB")->exec("EXPLAIN nf_users_settings;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}
		$result["settings"] = array();

		return $result;
	}

}