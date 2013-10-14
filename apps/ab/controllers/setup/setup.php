<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace apps\ab\controllers\setup;

use \timer as timer;
use \apps\ab\models as models;
class setup extends \apps\ab\controllers\_ {
	function __construct() {
		parent::__construct();
	}
	function page() {
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];


		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","apps/ab/ui/setup/");
		$tmpl->page = array(
			"section"=> "main",
			"sub_section"=> "",
			"template"=> "main",
			"meta"    => array(
				"title"=> "AB - Setup",
			)
		);


		$tmpl->output();

	}

}
