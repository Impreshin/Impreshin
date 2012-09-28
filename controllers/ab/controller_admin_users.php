<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace controllers\ab;
use \F3 as F3;
use \timer as timer;
use \models\ab as models;
class controller_admin_users {
	function __construct() {

	}
	function page() {
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];
		$cID = $user['publication']['cID'];




//test_array($pages);

		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","ui/ab/");
		$tmpl->page = array(
			"section"=> "admin",
			"sub_section"=> "users",
			"template"=> "page_admin_users",
			"meta"    => array(
				"title"=> "AdBooker - Admin - Users",
			),
			"help"=> "/ab/help/admin/users"
		);

		$permissions = models\user_permissions::permissions();

		$tmpl->permissions = $permissions['p'];
		$tmpl->permissions_desc = $permissions['d'];
		$tmpl->marketers = models\marketers::getAll("pID='$pID' AND cID='$cID'", "marketer ASC");;
		$tmpl->production = models\production::getAll("pID='$pID' AND cID='$cID'", "production ASC");;
		$tmpl->output();

	}

}
