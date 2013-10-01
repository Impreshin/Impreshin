<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace apps\nf\controllers;

use \timer as timer;
use \apps\nf\models as models;
class layout extends \apps\nf\controllers\_{
	function __construct() {
		parent::__construct();
	}
	function page() {
		$user = $this->f3->get("user");

//		if (!$user['permissions']['layout']['page']) $this->f3->error(404);
		$userID = $user['ID'];
		$pID = $user['pID'];




//test_array($user);

		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","apps/nf/ui/");
		$tmpl->page = array(
			"section"=> "layout",
			"sub_section"=> "planning",
			"template"=> "layout",
			"meta"    => array(
				"title"=> "NF - Layout",
			)
		);
		$tmpl->repeat_dates = \models\dates::getAll("pID='$pID' AND publish_date >= '" . $user['publication']['current_date']['publish_date'] . "'", "publish_date ASC", "");
		$tmpl->categories = models\categories::getAll("cID='". $user['company']['ID']."'","orderby ASC");
		$tmpl->sections = models\sections::getAll("pID='$pID'","section ASC");
		$tmpl->page_loading = \apps\ab\models\loading::getAll("pID='$pID'","pages ASC");
		//$tmpl->production = models\production::getAll("pID='$pID'", "production ASC");
		$tmpl->settings = models\settings::_read("layout");
		$tmpl->use_pub = true;
		$tmpl->output();

	}

}
