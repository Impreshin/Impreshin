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
					"edit_master"    => 0,
					"delete"  => 0,
					"invoice"  => 0,
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
				"account"=>array(
					"figures"=> array(
						"page"=> 0
					),

				),
				"marketer"=>array(
					"figures"=> array(
						"page"=> 0
					),
					"discounts"=> array(
						"page"=> 0
					),
					"targets"=> array(
						"page"=> 0
					)
				),
				"production"=>array(
					"figures"=> array(
						"page"=> 0
					)
				),


				"category"=>array(

					"figures"=> array(
						"page"=> 0
					)
				),



				"publication"=>array(
					"figures"=> array(
						"page"=> 0
					),
					"placing"=> array(
						"page"=> 0
					),
					"section"=> array(
						"page"=> 0
					),

				)


			),
			"administration"=>array(
				"application"=>array(
					"accounts"=> array(
						"page"=> 0,
						"status"=>array(
							"page"=>0
						)
					),
					"categories"=> array(
						"page"=> 0
					),
					"marketers"=> array(
						"page"=> 0,
						"targets"=>array(
							"page"=>0
						)
					),
					"production"=> array(
						"page"=> 0
					),
					"sections"=> array(
						"page"=> 0
					),
					"placing"=> array(
						"page"=> 0,
						"colours"=>array(
							"page"=> 0
						)
					),
					"loading"=> array(
						"page"=> 0
					),
				),
				"system"=>array(
					"dates"=>array(
						"page"=> 0
					),
					"users"=> array(
						"page"=> 0
					),
					"publications"=> array(
						"page"=> 0
					)
				)
			)

		);



		$return['d'] = array(

			"details"=>array(
				"actions"=>array(
					"edit_master"=>"this will override any edit locks there may be, like not being able to edit a record in archives / once it is placed"
				)
			),
			"layout"=> array(
				"pagecount"=> "Allows this user to set how many pages the edition will be"
			),
			"administration"=>array(
				"application"=>array(
					"accounts"=>array(
						"status"=>"Edit / Add the account status types"
					)
				)
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