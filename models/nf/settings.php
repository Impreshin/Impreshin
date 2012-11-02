<?php

namespace models\nf;
use \F3 as F3;
use \timer as timer;

class settings {
	function __construct() {


	}

	public static function settings() {
		$timer = new timer();
		$return = array();
		$columns = array(
			"heading"                 => array(
				"c"=> "heading",
				"o"=> "heading",
				"h"=> "Heading"
			),
			"datein"            => array(
				"c"=> "datein",
				"o"=> "datein",
				"h"=> "Captured&nbsp;Date",
				"m"=> 80
			),
			"cm"                   => array(
				"c"=> "cm",
				"o"=> "cm",
				"h"=> "Cm",
				"w"=> 60
			)

		);
			$return["columns"] = $columns;




		$cfg = F3::get("cfg");
		$groupByoptions = array(
			"none"               => array(
				"n"=> "No Ordering",
				"g"=> "none"
			)
		);


		$sections = array(

			"provisional"=> array(
				"none",
			),

		);

		$groupby = array();
		foreach ($sections as $key=> $value) {
			$opts = array();
			foreach ($value as $col) {
				$opts[] = $groupByoptions[$col];
			}
			$groupby[$key] = $opts;
		}




		$return["groupby"] = $groupby;



		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function defaults() {
		$timer = new timer();
		$return = array();

		$settings = array(
			"provisional"=>array(
				"col"        => array(
					"heading",
					"datein",
					"cm",
				),
				"group"      => array(
					"g"=> "none",
					"o"=> "ASC"
				),
				"order"      => array(
					"c"=> "heading",
					"o"=> "ASC"
				),
				"count"      => "5",
				"highlight"  => "checked",
				"filter"     => "*",
			)
		);

		$return['settings'] = $settings;




		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function _read($section, $permission=array()){
		$user = F3::get("user");
		$timer = new timer();
		$settings = self::settings($user['permissions']);
		$defaults = self::defaults();
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