<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace apps\pf\controllers\admin;

use \timer as timer;
use \apps\pf\models as models;
class users extends \apps\pf\controllers\_ {
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
		$tmpl = new \template("template.tmpl","apps/pf/ui/");
		$tmpl->page = array(
			"section"=> "admin",
			"sub_section"=> "users",
			"template"=> "admin_users",
			"meta"    => array(
				"title"=> "PF - Admin - Users",
			),
			//"help"=> "/apps/nf/help/admin/users"
		);

		$permissions = \apps\pf\permissions::_available($cID);
		//$tmpl->use_pub = true;
		$tmpl->permissions = $permissions['p'];
		$tmpl->permissions_desc = $permissions['d'];
		$tmpl->output();

	}

}
