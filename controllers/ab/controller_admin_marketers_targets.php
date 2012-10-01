<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace controllers\ab;
use \F3 as F3;
use \timer as timer;
use \models\ab as models;
class controller_admin_marketers_targets {
	function __construct() {

	}
	function page() {
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];
		$cID = $user['publication']['cID'];


		$where = "cID='$cID'";
		$records = models\marketers::getAll($where, " marketer ASC");

		$section = "admin_marketers_targets";

		$settings = models\settings::_read($section);



		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","ui/ab/");
		$tmpl->page = array(
			"section"=> "admin",
			"sub_section"=> "marketers_targets",
			"template"=> "page_admin_marketers_targets",
			"meta"    => array(
				"title"=> "AdBooker - Admin - Marketers - Targets",
			)
		);


		$tmpl->marketers = $records;
		$tmpl->settings = $settings;
		$tmpl->output();

	}

}
