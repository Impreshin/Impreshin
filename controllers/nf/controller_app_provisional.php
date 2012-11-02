<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace controllers\nf;
use \F3 as F3;
use \timer as timer;
use \models\nf as models;
use \models\user as user;
class controller_app_provisional {
	function __construct() {
		$user = F3::get("user");
		$userID = $user['ID'];
		if (!$userID) F3::reroute("/login");
	}
	function page() {

		$timer = new timer();
		$user = F3::get("user");
		//F3::get("DB")->exec("UPDATE global_users SET last_page = '" . $_SERVER['REQUEST_URI'] . "' WHERE ID = '" . $user['ID'] . "'");



		$userID = $user['ID'];
		$pID = $user['pID'];
		$currentDate = $user['publication']['current_date'];
		//test_array($user);
		$settings = models\settings::_read("provisional", $user['permissions']);






		//$settings = models\settings::_read("provisional",$user['permissions']);




		//test_array($settings);

		$tmpl = new \template("template.tmpl","ui/nf/");
		$tmpl->page = array(
			"section"=> "bookings",
			"sub_section"=> "provisional",
			"template"=> "page_app_provisional",
			"meta"    => array(
				"title"=> "NewsFiler - Provisional",
			)
		);





		$tmpl->settings = $settings;


		$tmpl->output();
		$timer->stop("Controller - ".__CLASS__." - ".__FUNCTION__, func_get_args());
	}


}
