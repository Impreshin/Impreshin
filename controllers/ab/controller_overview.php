<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace controllers\ab;
use \F3 as F3;
use \timer as timer;
use \models\ab as models;
class controller_overview {
	function __construct() {
		$user = F3::get("user");
		$userID = $user['ID'];
		if (!$userID) F3::reroute("/login");
		F3::get("DB")->exec("UPDATE global_users SET last_page = '" . $_SERVER['REQUEST_URI'] . "' WHERE ID = '" . $user['ID'] . "'");
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
			"section"=> "overview",
			"sub_section"=> "view",
			"template"=> "page_overview",
			"meta"    => array(
				"title"=> "AdBooker - Overview",
			)
		);
		$tmpl->repeat_dates = models\dates::getAll("pID='$pID' AND publish_date >= '" . $user['publication']['current_date']['publish_date'] . "'", "publish_date ASC", "");
		$tmpl->placing = models\placing::getAll("pID='$pID'");
		$tmpl->sections = models\sections::getAll("pID='$pID'");
		$tmpl->settings = $user['settings']['overview'];
		$tmpl->output();

	}

}