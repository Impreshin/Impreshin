<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace controllers\ab;
use \F3 as F3;
use \timer as timer;
use \models\ab as models;
class controller_admin_accounts_import {
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
			"sub_section"=> "accounts_import",
			"template"=> "page_admin_accounts_import",
			"meta"    => array(
				"title"=> "AB - Admin - Accounts - Import",
			)
		);
		$tmpl->publications = \models\publications::getAll("cID='$cID'", "publication ASC");
$tmpl->accountStatus = models\accountStatus::getAll("cID='$cID'", "orderby ASC");
		$tmpl->output();

	}

}
