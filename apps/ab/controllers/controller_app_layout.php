<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace apps\ab\controllers;

use \timer as timer;
use \apps\ab\models as models;
class controller_app_layout extends \apps\ab\controllers\_{
	function __construct() {
		parent::__construct();
	}
	function page() {
		$user = $this->f3->get("user");
		if (!$user['permissions']['layout']['page']) $this->f3->error(404);
		$userID = $user['ID'];
		$pID = $user['pID'];




//test_array($pages);

		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","apps/ab/ui/");
		$tmpl->page = array(
			"section"=> "layout",
			"sub_section"=> "planning",
			"template"=> "page_app_layout",
			"meta"    => array(
				"title"=> "AB - Layout",
			)
		);
		$tmpl->repeat_dates = \models\dates::getAll("pID='$pID' AND publish_date >= '" . $user['publication']['current_date']['publish_date'] . "'", "publish_date ASC", "");
		$tmpl->placing = models\placing::getAll("pID='$pID'");
		$tmpl->sections = models\sections::getAll("pID='$pID'","section ASC");
		$tmpl->page_loading = models\loading::getAll("pID='$pID'","pages ASC");
		$tmpl->production = models\production::getAll("pID='$pID'", "production ASC");
		$tmpl->settings = models\settings::_read("layout");
		$tmpl->output();

	}

}
