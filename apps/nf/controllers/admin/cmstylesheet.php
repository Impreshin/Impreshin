<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace apps\nf\controllers\admin;

use \timer as timer;
use \apps\nf\models as models;
class cmstylesheet extends \apps\nf\controllers\_ {
	function __construct() {
		parent::__construct();
	}
	function page() {
		$user = $this->f3->get("user");
		$userID = $user['ID'];

		$cID = $user['company']['ID'];



//test_array($pages);

		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","apps/nf/ui/");
		$tmpl->page = array(
			"section"=> "admin",
			"sub_section"=> "cmstylesheet",
			"template"=> "admin_cmstylesheet",
			"meta"    => array(
				"title"=> "NF - Admin - Cm Style SHeet",
			), 
			"js" => array(),
			"css" => array(),
		);

		
		$tmpl->output();

	}

}
