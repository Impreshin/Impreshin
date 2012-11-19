<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace controllers\ab;
use \F3 as F3;
use \timer as timer;
use models\ab as models;
class controller_app_deleted {
	function __construct() {



	}
	function page() {
		$user = F3::get("user");
		if (!$user['permissions']['records']['deleted']['page']) F3::error(404);
		$userID = $user['ID'];
		$pID = $user['pID'];

		//test_array($user);
		$ab_settings = F3::get("settings");
		$settings = models\settings::_read("deleted");


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


		$dates = models\dates::getAll("pID='$pID' AND publish_date <= '" . $user['publication']['current_date']['publish_date'] . "'", "publish_date DESC", "0,5");

		//test_array($ab_settings);

		$date_range = F3::get("DB")->exec("SELECT min(publish_date) as earliestDate, max(publish_date) as latestDate FROM global_dates WHERE pID = '$pID'");
		if (count($date_range)) {
			$date_range = $date_range[0];
		}


		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","ui/ab/");
		$tmpl->page = array(
			"section"=> "records",
			"sub_section"=> "deleted",
			"template"=> "page_app_deleted",
			"print"=> "/ab/print/deleted",
			"meta"    => array(
				"title"=> "AB - Deleted Bookings",
			)
		);



		$tmpl->date_range = json_encode($date_range);

		$tmpl->production = models\production::getAll("pID='$pID'","production ASC");
		$tmpl->dates = $dates;



		$tmpl->settings = $settings;

		$tmpl->settings_columns = array(
			"selected"=> $selected,
			"available"=> $available
		);
		$tmpl->output();

	}

	function _print() {
		$timer = new timer();
		$user = F3::get("user");
		if (!$user['permissions']['records']['deleted']['page']) F3::error(404);
		$settings = models\settings::_read("deleted", $user['permissions']);


		$dataO = new \controllers\ab\data\deleted();
		$data = $dataO->_list(true);


		$tmpl = new \template("template.tmpl", "ui/ab/print/",true);
		$tmpl->page = array(
			"section"    => "bookings",
			"sub_section"=> "deleted",
			"template"   => "page_app_deleted",
			"meta"       => array(
				"title"=> "AB - Print - Deleted",
			)
		);

		$tmpl->settings = $settings;
		$tmpl->data = $data;

		//test_array($data);

		$tmpl->output();
		$timer->stop("Controller - " . __CLASS__ . " - " . __FUNCTION__, func_get_args());
	}

}
