<?php

namespace models\nf;
use \F3 as F3;
use \timer as timer;

class settings {
	function __construct() {


	}

	public static function getSettings($permissions=array()) {
		$timer = new timer();
		$return = array();





		$cfg = F3::get("cfg");




		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getDefaults($application = "ab", $ID = "") {
		$timer = new timer();
		$return = array();
			$settings = array(


			);

		$return['settings'] = $settings;



		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function _read($section, $permission=array()){
		$user = F3::get("user");
		$timer = new timer();
		$settings = self::getSettings($user['permissions']);
		$defaults = self::getDefaults();
		$settings_raw = $settings;

		$user_settings = new user_settings();
		$user_settings = $user_settings->_read($user['ID']);
		$user_settings['settings'] = @unserialize($user_settings['settings']);







		if ($user_settings['settings']){
			$user_settings = array_replace_recursive((array)$defaults, (array)($user_settings) ? $user_settings : array());
		} else {
			$user_settings = $defaults;
		}





		$return = array();

		//test_array($user_settings);
		$return = $user_settings['settings'][$section];





		if (isset($user_settings['settings'][$section]['col']) && count($settings["columns"])) {
			$columns = array();

			foreach ($user_settings['settings'][$section]['col'] as $col){
				if (isset($settings['columns'][$col])){
					$columns[] = $settings['columns'][$col];
				}

			}



			$return['col'] = $columns;
			$return['count']=count($columns);
		}
		if (isset($settings_raw['groupby'][$section])) $return['groupby']= $settings_raw['groupby'][$section];




		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}
	function write(){

	}

}