<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace controllers\ab;
use \F3 as F3;
use \timer as timer;
use \models\ab as models;
class controller_production {
	function __construct() {


	}
	function page() {
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];

		//test_array($user);
		$ab_settings = F3::get("settings");
		$settings = models\settings::_read("production");

		//test_array($settings);


		$a = array();
		$b = array();

		foreach ($settings['col'] as $col) {
			$a[] = $col;
			$b[] = $col['c'];

		}


		$selected = $a;
		$available = array();
		foreach ($ab_settings["columns"] as $col) {
			if (!in_array($col['c'], $b)) {
				$available[] = $col;
			}

		}



		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","ui/adbooker/");
		$tmpl->page = array(
			"section"=> "production",
			"sub_section"=> "list",
			"template"=> "page_production",
			"meta"    => array(
				"title"=> "AdBooker - Production",
			)
		);

		$tmpl->production = models\production::getAll("pID='$pID'","production ASC");
		$tmpl->repeat_dates = models\dates::getAll("pID='$pID' AND publish_date >= '" . $user['publication']['current_date']['publish_date'] . "'", "publish_date ASC", "");


		$tmpl->settings = $settings;

		$tmpl->settings_columns = array(
			"selected"=> $selected,
			"available"=> $available
		);
		$tmpl->output();

	}
function t(){
	return "go die";
}

}
