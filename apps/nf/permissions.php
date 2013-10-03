<?php

namespace apps\nf;


use \timer as timer;

class permissions {
	function __construct() {


	}

	public static function _available() {
		$timer = new timer();
		$f3 = \Base::instance();
		$return = array();
		$return['p'] = array(
			"form"           => array(
				"new"         => 0,
				"edit"        => 0,
				"delete"      => 0,
				"edit_master" => 0,
				"author_dropdown"=>0
			),
			"details"           => array(
				"overwrite_locked"         => 0,
				"placed"         => 0,
				
			),
			"layout"         => array(
				"page"      => 0,
				"pagecount" => 0,
				"editpage"  => 0
			),
			"records"        => array(
				"deleted" => array(
					"page" => 0,
				),
				"search"  => array(
					"page" => 0
				)
			),
			"administration" => array(
				"application" => array(

				),
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


		$return['d'] = array(

			"administration" => array(

				"system"      => array(
					"dates"        => array(
						"page" => ""
					),
					"users"        => array(
						"page" => ""
					),
					"publications" => array(
						"page" => ""
					)
				)
			)
		);


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}
	public static function defaults() {
		$timer = new timer();

		$return = self::_available();;



		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}



}