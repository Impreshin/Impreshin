<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace controllers\ab;
use \F3 as F3;
use \timer as timer;
use \models\ab as models;
use \models\user as user;
class controller_details {
	function __construct() {
		$user = F3::get("user");
		$userID = $user['ID'];
		if (!$userID) F3::reroute("/login");
	}


	function _print() {
		$timer = new timer();
		$user = F3::get("user");


		$dataO = new \controllers\ab\data\data();
		$data = $dataO->details();

		//test_array($data);

		$tmpl = new \template("template.tmpl","ui/adbooker/print/");
		$tmpl->page = array(
			"section"=> "bookings",
			"sub_section"=> "provisional",
			"template"=> "page_details",
			"meta"    => array(
				"title"=> "AdBooker - Print - ".$data['client'],
			)
		);

		$tmpl->data=$data;

		//test_array($data);

		$tmpl->output();
		$timer->stop("Controller - ".__CLASS__." - ".__FUNCTION__, func_get_args());
	}


}
