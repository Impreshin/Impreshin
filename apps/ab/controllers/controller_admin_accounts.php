<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace apps\ab\controllers;

use \timer as timer;
use \apps\ab\models as models;
class controller_admin_accounts extends \apps\ab\controllers\_ {
	function __construct() {
		parent::__construct();
	}
	function page() {
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];

		$accountStatus = models\accountStatus::getAll("cID='$cID'","orderby ASC");


//test_array($pages);

		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","apps/ab/ui/");
		$tmpl->page = array(
			"section"=> "admin",
			"sub_section"=> "accounts",
			"template"=> "page_admin_accounts",
			"meta"    => array(
				"title"=> "AB - Admin - Accounts",
			)
		);


		$tmpl->accountStatus = $accountStatus;
		$tmpl->output();

	}

}
