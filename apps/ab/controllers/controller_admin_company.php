<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace apps\ab\controllers;

use \timer as timer;
use \apps\ab\models as models;
class controller_admin_company extends \apps\ab\controllers\_ {
	function __construct() {
		parent::__construct();
	}
	function page() {
		$user = $this->f3->get("user");
		$uID = $user['ID'];
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];

		//test_array($user);


		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","apps/ab/ui/");
		$tmpl->page = array(
			"section"=> "admin",
			"sub_section"=> "company",
			"template"=> "page_admin_company",
			"meta"    => array(
				"title"=> "AB - Admin - Company",
			)
		);

		$timezones = \DateTimeZone::listIdentifiers();
		
		//test_array($timezones); 

		$tmpl->use_pub=false;
		$tmpl->timezones= $timezones;

		$tmpl->output();

	}

}
