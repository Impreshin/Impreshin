<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace apps\ab\controllers;

use \timer as timer;
use \apps\ab\models as models;
class controller_admin_users extends \apps\ab\controllers\_ {
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
			"sub_section"=> "users",
			"template"=> "page_admin_users",
			"meta"    => array(
				"title"=> "AB - Admin - Users",
			),
			"help"=> "/apps/ab/help/admin/users"
		);

		$permissions = \apps\ab\permissions::_available();

		$tmpl->permissions = $permissions['p'];
		$tmpl->permissions_desc = $permissions['d'];
		$tmpl->marketers = models\marketers::getAll("pID='$pID' AND cID='$cID'", "marketer ASC");;
		$tmpl->production = models\production::getAll("pID='$pID' AND cID='$cID'", "production ASC");;
		$tmpl->output();

	}

}
