<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace controllers\ab;
use \F3 as F3;
use \timer as timer;
use \models\ab as models;
class controller_admin_production {
	function __construct() {

	}
	function page() {
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];
		$cID = $user['publication']['cID'];


		$users = \models\user::getAll("cID='$cID'", "fullName ASC");


//test_array($pages);

		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","ui/ab/");
		$tmpl->page = array(
			"section"=> "admin",
			"sub_section"=> "production",
			"template"=> "page_admin_production",
			"meta"    => array(
				"title"=> "AB - Admin - Production",
			)
		);


		$tmpl->users = $users;
		$tmpl->output();

	}

}
