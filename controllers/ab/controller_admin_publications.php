<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace controllers\ab;
use \F3 as F3;
use \timer as timer;
use \models\ab as models;
class controller_admin_publications {
	function __construct() {
		$this->f3 = \base::instance();
	}
	function page() {
		$user = $this->f3->get("user");
		$uID = $user['ID'];
		$pID = $user['pID'];
		$cID = $user['publication']['cID'];

		//test_array($user);


		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","ui/ab/");
		$tmpl->page = array(
			"section"=> "admin",
			"sub_section"=> "publications",
			"template"=> "page_admin_publications",
			"meta"    => array(
				"title"=> "AB - Admin - Publications",
			)
		);



		$tmpl->output();

	}

}
