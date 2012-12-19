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
			"details"        => array(
				"actions" => array(
					"check"    => 0,
					"material" => 0,
					"repeat"   => 0,
					"invoice"  => 0,
				),
				"fields"  => array(
					"rate"          => 0,
					"totalCost"     => 0,
					"totalShouldbe" => 0
				),
			),
			"lists"          => array(
				"fields" => array(
					"rate"          => 0,
					"totalCost"     => 0,
					"totalShouldbe" => 0
				),
				"totals" => array(
					"totalCost" => 0,
				)
			),
			"form"           => array(

				"new"         => 0,
				"edit"        => 0,
				"delete"      => 0,
				"edit_master" => 0
			),
			"production"     => array(
				"page" => 0
			),
			"layout"         => array(
				"page"      => 0,
				"pagecount" => 0,
				"editpage"  => 0
			),
			"overview"       => array(
				"page" => 0
			),
			"records"        => array(
				"deleted" => array(
					"page" => 0,
				),
				"search"  => array(
					"page" => 0
				)
			),
			"reports"        => array(
				"account"     => array(
					"figures"   => array(
						"page" => 0
					),
					"discounts" => array(
						"page" => 0
					),

				),
				"marketer"    => array(
					"figures"   => array(
						"page" => 0
					),
					"discounts" => array(
						"page" => 0
					),
					"targets"   => array(
						"page" => 0
					)
				),
				"production"  => array(
					"figures" => array(
						"page" => 0
					)
				),
				"category"    => array(

					"figures"   => array(
						"page" => 0
					),
					"discounts" => array(
						"page" => 0
					),
				),
				"publication" => array(
					"figures"   => array(
						"page" => 0
					),
					"discounts" => array(
						"page" => 0
					),
					"placing"   => array(
						"page" => 0
					),
					"section"   => array(
						"page" => 0
					),

				)


			),
			"administration" => array(
				"application" => array(
					"accounts"      => array(
						"page"   => 0,
						"status" => array(
							"page" => 0
						)
					),
					"categories"    => array(
						"page" => 0
					),
					"marketers"     => array(
						"page"    => 0,
						"targets" => array(
							"page" => 0
						)
					),
					"production"    => array(
						"page" => 0
					),
					"sections"      => array(
						"page" => 0
					),
					"placing"       => array(
						"page"    => 0,
						"colours" => array(
							"page" => 0
						)
					),
					"loading"       => array(
						"page" => 0
					),
					"inserts_types" => array(
						"page" => 0
					),
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
			"details"        => array(
				"actions" => array(
					"check"    => "Gives the user permission to be able to 'check' records",
					"material" => "Gives the user permission to be able to change the material status",
					"repeat"   => "Allows the user to repeat this booking",
					"invoice"  => "If ticked the invoice button apears allowing the user to capture an invoice number",
				),
				"fields"  => array(
					"rate"          => "If checked the user can view this field on the details pane",
					"totalCost"     => "Net Cost on the details pane",
					"totalShouldbe" => "Gross Cost on the details pane"
				),
			),
			"lists"          => array(
				"fields" => array(
					"rate"          => "If checked the user can add this field on the list pages they have access to",
					"totalCost"     => "Net Cost in the lists",
					"totalShouldbe" => "Gross Cost in the lists"
				),
				"totals" => array(
					"totalCost" => "",
				)
			),
			"form"           => array(

				"new"         => "Gives the user permission to be able to capture a booking",
				"edit"        => "Gives the user permission to be able to edit bookings",
				"delete"      => "Gives the user permission to be able to delete bookings",
				"edit_master" => "This will override any edit locks there may be, like not being able to edit a record in archives / once it is placed.<br> Usualy only the big chief gets this permission"
			),
			"production"     => array(
				"page" => "The user will have access to the production pages"
			),
			"layout"         => array(
				"page"      => "the user will have access to the layout page. this includes the user being able to 'plan' adverts onto pages",
				"pagecount" => "Allows this user to set how many pages the edition will be",
				"editpage"  => "When ticked the user can change the page settings like colour / section etc"
			),
			"overview"       => array(
				"page" => "The user will have access to view the overview page"
			),
			"records"        => array(
				"deleted" => array(
					"page" => "The user will have access to view the deleted records page. all deleted records are stored and accessible from this page",
				),
				"search"  => array(
					"page" => "The user will have access to view the search page. "
				)
			),
			"reports"        => array(
				"account"     => array(
					"figures"   => array(
						"page" => ""
					),
					"discounts" => array(
						"page" => ""
					),

				),
				"marketer"    => array(
					"figures"   => array(
						"page" => ""
					),
					"discounts" => array(
						"page" => ""
					),
					"targets"   => array(
						"page" => ""
					)
				),
				"production"  => array(
					"figures" => array(
						"page" => ""
					)
				),
				"category"    => array(

					"figures"   => array(
						"page" => ""
					),
					"discounts" => array(
						"page" => ""
					),
				),
				"publication" => array(
					"figures"   => array(
						"page" => ""
					),
					"discounts" => array(
						"page" => ""
					),
					"placing"   => array(
						"page" => ""
					),
					"section"   => array(
						"page" => ""
					),

				)


			),
			"administration" => array(
				"application" => array(
					"accounts"      => array(
						"page"   => "",
						"status" => array(
							"page" => ""
						)
					),
					"categories"    => array(
						"page" => ""
					),
					"marketers"     => array(
						"page"    => "",
						"targets" => array(
							"page" => ""
						)
					),
					"production"    => array(
						"page" => ""
					),
					"sections"      => array(
						"page" => ""
					),
					"placing"       => array(
						"page"    => "",
						"colours" => array(
							"page" => ""
						)
					),
					"loading"       => array(
						"page" => ""
					),
					"inserts_types" => array(
						"page" => ""
					),
				),
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
		$a = new \Axon("global_users_company");
		$a->load("uID='$uID' AND cID = '$cID'");
		$a->ab_permissions = serialize($values);

		if (!$a->dry()) $a->save();


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return "done";
	}

}