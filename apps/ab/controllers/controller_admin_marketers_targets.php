<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace apps\ab\controllers;

use \timer as timer;
use \apps\ab\models as models;
class controller_admin_marketers_targets extends \apps\ab\controllers\_ {
	function __construct() {
		parent::__construct();
	}
	function page() {
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];


		$where = "cID='$cID'";
		$records = models\marketers::getAll($where, " marketer ASC");

		$section = "admin_marketers_targets";

		$settings = models\settings::_read($section);



		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","apps/ab/ui/");
		$tmpl->page = array(
			"section"=> "admin",
			"sub_section"=> "marketers_targets",
			"template"=> "page_admin_marketers_targets",
			"meta"    => array(
				"title"=> "AB - Admin - Marketers - Targets",
			)
		);


		$tmpl->marketers = $records;
		$tmpl->settings = $settings;
		$tmpl->output();

	}

}
