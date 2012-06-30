<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace controllers\ab;
use \F3 as F3;
use \timer as timer;
use \models\ab as models;
class controller_admin_marketers {
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
		$tmpl = new \template("template.tmpl","ui/adbooker/");
		$tmpl->page = array(
			"section"=> "admin",
			"sub_section"=> "marketers",
			"template"=> "page_admin_marketers",
			"meta"    => array(
				"title"=> "AdBooker - Admin - Marketers",
			)
		);


		$tmpl->users = $users;
		$tmpl->output();

	}

}
