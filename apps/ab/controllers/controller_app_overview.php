<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace apps\ab\controllers;

use \timer as timer;
use \apps\ab\models as models;
class controller_app_overview extends \apps\ab\controllers\_ {
	function __construct() {
		parent::__construct();

	}
	function page() {
		$user = $this->f3->get("user");
		if (!$user['permissions']['overview']['page']) $this->f3->error(404);
		$userID = $user['ID'];
		$pID = $user['pID'];




//test_array($pages);

		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","apps/ab/ui/");
		$tmpl->page = array(
			"section"=> "overview",
			"sub_section"=> "view",
			"template"=> "page_app_overview",
			"meta"    => array(
				"title"=> "AB - Overview",
			)
		);
		$tmpl->repeat_dates = \models\dates::getAll("pID='$pID' AND publish_date >= '" . $user['publication']['current_date']['publish_date'] . "'", "publish_date ASC", "");
		$tmpl->placing = models\placing::getAll("pID='$pID'");
		$tmpl->sections = models\sections::getAll("pID='$pID'");
		$tmpl->settings = models\settings::_read("overview");
		$tmpl->output();

	}

}
