<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace apps\ab\controllers;

use \timer as timer;
use \apps\ab\models as models;
class controller_admin_production extends \apps\ab\controllers\_ {
	function __construct() {
		parent::__construct();
	}
	function page() {
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];
		$cID = $user['publication']['cID'];


		$users = \models\user::getAll("cID='$cID'", "fullName ASC");


//test_array($pages);

		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","apps/ab/ui/");
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
