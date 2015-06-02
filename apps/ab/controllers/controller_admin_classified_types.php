<?php

namespace apps\ab\controllers;

use \timer as timer;
use \apps\ab\models as models;
class controller_admin_classified_types extends \apps\ab\controllers\_ {
	function __construct() {
		parent::__construct();
	}
	function page() {
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];




//test_array($pages);

		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","apps/ab/ui/");
		$tmpl->page = array(
			"section"=> "admin",
			"sub_section"=> "classified_types",
			"template"=> "page_admin_classified_types",
			"meta"    => array(
				"title"=> "AB - Admin - Classified Types",
			)
		);


		$tmpl->output();

	}

}
