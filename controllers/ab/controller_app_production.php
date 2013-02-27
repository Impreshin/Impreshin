<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace controllers\ab;
use \F3 as F3;
use \timer as timer;
use \models\ab as models;
class controller_app_production {
	function __construct() {

		$this->f3 = \base::instance();
	}
	function page() {
		$user = $this->f3->get("user");
		if (!$user['permissions']['production']['page']) $this->f3->error(404);
		$userID = $user['ID'];
		$pID = $user['pID'];

		//test_array($user);
		$ab_settings = $this->f3->get("settings");
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
		$tmpl = new \template("template.tmpl","ui/ab/");
		$tmpl->page = array(
			"section"=> "production",
			"sub_section"=> "list",
			"template"=> "page_app_production",
			"print"=> "/ab/print/production",
			"meta"    => array(
				"title"=> "AB - Production",
			)
		);

		$tmpl->production = models\production::getAll("pID='$pID'","production ASC");
		$tmpl->repeat_dates = \models\dates::getAll("pID='$pID' AND publish_date >= '" . $user['publication']['current_date']['publish_date'] . "'", "publish_date ASC", "");


		$tmpl->settings = $settings;

		$tmpl->settings_columns = array(
			"selected"=> $selected,
			"available"=> $available
		);
		$tmpl->output();

	}

	function _print() {
		$timer = new timer();
		$user = $this->f3->get("user");
		if (!$user['permissions']['production']['page']) $this->f3->error(404);

		$settings = models\settings::_read("production", $user['permissions']);


		$dataO = new \controllers\ab\data\production();
		$data = $dataO->_list();

		//test_array($data);

		$tmpl = new \template("template.tmpl", "ui/ab/print/",true);
		$tmpl->page = array(
			"section"    => "bookings",
			"sub_section"=> "production",
			"template"   => "page_app_production",
			"meta"       => array(
				"title"=> "AB - Print - Production",
			)
		);

		$tmpl->settings = $settings;
		$tmpl->data = $data;

		//test_array($data);

		$tmpl->output();
		$timer->stop("Controller - " . __CLASS__ . " - " . __FUNCTION__, func_get_args());
	}

}
