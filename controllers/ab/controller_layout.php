<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace controllers\ab;
use \F3 as F3;
use \timer as timer;
use \models\ab as models;
class controller_layout {
	function __construct() {
		$user = F3::get("user");
		$userID = $user['ID'];
		//if (!$userID) F3::reroute("/login");
		models\user_settings::save_config(array("page"=> $_SERVER['REQUEST_URI']));
	}
	function page() {
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];




//test_array($pages);

		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","ui/adbooker/");
		$tmpl->page = array(
			"section"=> "layout",
			"sub_section"=> "planning",
			"template"=> "page_layout",
			"meta"    => array(
				"title"=> "AdBooker - Layout",
			)
		);

		$tmpl->placing = models\placing::getAll("pID='$pID'");
		$tmpl->sections = models\sections::getAll("pID='$pID'");
		$tmpl->output();

	}

}
