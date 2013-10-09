<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace apps\ab\controllers;

use \timer as timer;
use \apps\ab\models as models;
class controller_app_deleted extends \apps\ab\controllers\_{
	function __construct() {
		parent::__construct();


	}
	function page() {
		$user = $this->f3->get("user");
		if (!$user['permissions']['records']['deleted']['page']) $this->f3->error(404);
		$userID = $user['ID'];
		$pID = $user['pID'];

		//test_array($user);
		$ab_settings = \apps\ab\settings::_available();
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


		$dates = \models\dates::getAll("pID='$pID' AND publish_date <= '" . $user['publication']['current_date']['publish_date'] . "'", "publish_date DESC", "0,5");

		//test_array($ab_settings);

		$date_range = $this->f3->get("DB")->exec("SELECT min(publish_date) as earliestDate, max(publish_date) as latestDate FROM global_dates WHERE pID = '$pID'");
		if (count($date_range)) {
			$date_range = $date_range[0];
		}


		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","apps/ab/ui/");
		$tmpl->page = array(
			"section"=> "records",
			"sub_section"=> "deleted",
			"template"=> "page_app_deleted",
			"print"=> "/app/ab/records/deleted/print",
			"meta"    => array(
				"title"=> "AB - Deleted Bookings",
			)
		);


		$tmpl->repeat_dates = \models\dates::getAll("pID='$pID' AND publish_date >= '" . $user['publication']['current_date']['publish_date'] . "'", "publish_date ASC", "");
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
		$user = $this->f3->get("user");
		if (!$user['permissions']['records']['deleted']['page']) $this->f3->error(404);
		$settings = models\settings::_read("deleted", $user['permissions']);


		$dataO = new \apps\ab\controllers\data\deleted();
		$data = $dataO->_list(true);


		$tmpl = new \template("template.tmpl", "apps/ab/ui/print/",true);
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
