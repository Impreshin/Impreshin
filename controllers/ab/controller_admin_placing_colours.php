<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace controllers\ab;
use \F3 as F3;
use \timer as timer;
use \models\ab as models;
class controller_admin_placing_colours {
	function __construct() {
		$this->f3 = \base::instance();
	}
	function page() {
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];
		$cID = $user['publication']['cID'];




//test_array($pages);

		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","ui/ab/");
		$tmpl->page = array(
			"section"=> "admin",
			"sub_section"=> "placing_colours",
			"template"=> "page_admin_placing_colours",
			"meta"    => array(
				"title"=> "AB - Admin - Placing - Colours",
			)
		);


		$tmpl->placing = models\placing::getAll("pID='$pID'","orderby ASC");
		$tmpl->output();

	}

}
