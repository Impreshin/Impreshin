<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace controllers\ab;
use \F3 as F3;
use \timer as timer;
use \models\ab as models;
class controller_app_overview {
	function __construct() {

	}
	function page() {
		$user = F3::get("user");
		if (!$user['permissions']['overview']['page']) F3::error(404);
		$userID = $user['ID'];
		$pID = $user['pID'];




//test_array($pages);

		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","ui/ab/");
		$tmpl->page = array(
			"section"=> "overview",
			"sub_section"=> "view",
			"template"=> "page_app_overview",
			"meta"    => array(
				"title"=> "AB - Overview",
			)
		);
		$tmpl->repeat_dates = models\dates::getAll("pID='$pID' AND publish_date >= '" . $user['publication']['current_date']['publish_date'] . "'", "publish_date ASC", "");
		$tmpl->placing = models\placing::getAll("pID='$pID'");
		$tmpl->sections = models\sections::getAll("pID='$pID'");
		$tmpl->settings = models\settings::_read("overview");
		$tmpl->output();

	}

}
