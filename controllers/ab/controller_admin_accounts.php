<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace controllers\ab;
use \F3 as F3;
use \timer as timer;
use \models\ab as models;
class controller_admin_accounts {
	function __construct() {

	}
	function page() {
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];
		$cID = $user['publication']['cID'];

		$accountStatus = models\accountStatus::getAll("cID='$cID'","orderby ASC");


//test_array($pages);

		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","ui/adbooker/");
		$tmpl->page = array(
			"section"=> "admin",
			"sub_section"=> "accounts",
			"template"=> "page_admin_accounts",
			"meta"    => array(
				"title"=> "AdBooker - Admin - Accounts",
			)
		);


		$tmpl->accountStatus = $accountStatus;
		$tmpl->output();

	}

}
