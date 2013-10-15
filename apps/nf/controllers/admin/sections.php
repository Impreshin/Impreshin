<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace apps\nf\controllers\admin;

use \timer as timer;
use \apps\nf\models as models;
class sections extends \apps\nf\controllers\_ {
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
			"sub_section"=> "sections",
			"template"=> "admin_sections",
			"meta"    => array(
				"title"=> "NF - Admin - Sections",
			),
			"css"=>"/ui/_css/plugins/jquery.miniColors.css",
			"js"=>"/ui/_js/plugins/jquery.miniColors.js"
		);

		$tmpl->use_pub = true;
		$tmpl->output();

	}

}
