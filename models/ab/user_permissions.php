<?php

namespace models\ab;
use \F3 as F3;
use \timer as timer;

class user_permissions {
	function __construct() {


	}

	public static function permissions() {
		$timer = new timer();
		$return = array();
		$return['p'] = array(


			"details"=> array(
				"actions"=> array(
					"check"   => 0,
					"material"=> 0,
					"repeat"  => 0,
					"edit"    => 0,
					"delete"  => 0,
				),
				"fields"=> array(
					"rate"         => 0,
					"totalCost"    => 0,
					"totalShouldbe"=> 0
				),
			),
			"lists"=>array(
				"fields"=> array(
					"rate"         => 0,
					"totalCost"    => 0,
					"totalShouldbe"=> 0
				),
				"totals"=>array(
					"totalCost"=>0,
				)
			),
			"form"=>array(
				"page"=>0,
			),
			"provisional"=>array(
				"page"=>0
			),
			"production"=>array(
				"page"=>0
			),
			"layout"=>array(
				"page"=>0,
				"pagecount"=>0,
				"editpage"=>0
			),
			"overview"=>array(
				"page"=>0
			),
			"records"=>array(
				"deleted"=>array(
					"page"=>0,
				),
				"search"=>array(
					"page"=>0
				)
			),
			"reports"=>array(

			),
			"administration"=>array(
				"accounts"=> array(
					"page"=> 0
				),
				"categories"=> array(
					"page"=> 0
				),
				"marketers"=> array(
					"page"=> 0
				),
				"production"=> array(
					"page"=> 0
				),
				"sections"=> array(
					"page"=> 0
				),
				"placing"=> array(
					"page"=> 0
				),
				"loading"=> array(
					"page"=> 0
				),
				"dates"=>array(
					"page"=> 0
				),
				"users"=> array(
					"page"=> 0
				),
				"publications"=> array(
					"page"=> 0
				),
				"company"=>array(
					"page"=> 0
				)
			)

		);



		$return['d'] = array(

			"layout"=> array(
				"pagecount"=> "Allows this user to set how many pages the edition will be"
			)
		);


		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}



	public static function _read($user_permissions){




		$timer = new timer();
		$permissions = self::permissions();
		$permissions = $permissions['p'];




		if (count($user_permissions)) {
			$user_permissions= @unserialize($user_permissions);
			$user_permissions= array_replace_recursive((array)$permissions, (array)($user_permissions) ? $user_permissions : array());
		} else {
			$user_permissions = $permissions;
		}











		$return = array();

		//test_array($user_settings);
		$return = $user_permissions;









		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function write($uID,$cID,$values){
		$timer = new timer();
		$a = new \Axon("global_users_company");
		$a->load("uID='$uID' AND cID = '$cID'");
		$a->ab_permissions = serialize($values);

		if (!$a->dry()) $a->save();



		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return "done";
	}

}