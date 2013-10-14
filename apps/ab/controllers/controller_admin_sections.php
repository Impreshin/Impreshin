<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace apps\ab\controllers;

use \timer as timer;
use \apps\ab\models as models;
class controller_admin_sections extends \apps\ab\controllers\_ {
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
		$tmpl = new \template("template.tmpl","apps/ab/ui/");
		$tmpl->page = array(
			"section"=> "admin",
			"sub_section"=> "sections",
			"template"=> "page_admin_sections",
			"meta"    => array(
				"title"=> "AB - Admin - Sections",
			),
			"css"=>"/ui/_css/plugins/jquery.miniColors.css",
			"js"=>"/ui/_js/plugins/jquery.miniColors.js"
		);


		$tmpl->output();

	}

}
