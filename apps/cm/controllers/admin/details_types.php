<?php

namespace apps\cm\controllers\admin;

use \timer as timer;
use \apps\cm\models as models;
class details_types extends \apps\cm\controllers\_ {
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
		$tmpl = new \template("template.tmpl","apps/cm/ui/");
		$tmpl->page = array(
			"section"=> "admin",
			"sub_section"=> "details_types",
			"template"=> "admin_details_types",
			"meta"    => array(
				"title"=> "CM - Admin - Details Types",
			),
			//"help"=> "/apps/nf/help/admin/users"
		);

		$permissions = \apps\cm\permissions::_available($cID);
		//$tmpl->use_pub = true;
		$tmpl->permissions = $permissions['p'];
		$tmpl->permissions_desc = $permissions['d'];
		$tmpl->output();

	}

}
