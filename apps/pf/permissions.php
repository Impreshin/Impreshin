<?php

namespace apps\pf;


use \timer as timer;

class permissions {
	function __construct() {


	}

	public static function _available($cID="") {
		$timer = new timer();
		$f3 = \Base::instance();
		$return = array();
		$return['p'] = array(
			"front"=>array(
				"upload_pdf"=>0,
				"download_pdf"=>0,
			),
			"administration"=>array(
				"application" => array(),
			    "system"=>array(
				   
				    "users"        => array(
					    "page" => 0
				    ),
				   
			    )
			)

		);


		$return['d'] = array(
			
		);
		
		

		


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}
	public static function defaults($cID="") {
		$timer = new timer();

		$return = self::_available($cID);;



		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}



}