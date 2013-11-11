<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace apps\nf\controllers\admin;

use \timer as timer;
use \apps\nf\models as models;
class users extends \apps\nf\controllers\_ {
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
		$tmpl = new \template("template.tmpl","apps/nf/ui/");
		$tmpl->page = array(
			"section"=> "admin",
			"sub_section"=> "users",
			"template"=> "admin_users",
			"meta"    => array(
				"title"=> "NF - Admin - Users",
			),
			"help"=> "/apps/nf/help/admin/users"
		);

		$permissions = \apps\nf\permissions::_available($cID);
		//$tmpl->use_pub = true;
		$tmpl->permissions = $permissions['p'];
		$tmpl->permissions_desc = $permissions['d'];
		$tmpl->output();

	}

}
