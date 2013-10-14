<?php

namespace apps\nf\controllers\admin;

use \timer as timer;
use \apps\nf\models as models;
class publications extends \apps\nf\controllers\_ {
	function __construct() {
		parent::__construct();
	}
	function page() {
		$user = $this->f3->get("user");
		$uID = $user['ID'];
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];

		//test_array($user);


		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","apps/nf/ui/");
		$tmpl->page = array(
			"section"=> "admin",
			"sub_section"=> "publications",
			"template"=> "admin_publications",
			"meta"    => array(
				"title"=> "NF - Admin - Publications",
			)
		);


		$tmpl->use_pub=false;

		$tmpl->output();

	}

}
