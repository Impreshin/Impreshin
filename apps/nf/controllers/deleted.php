<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace apps\nf\controllers;

use \timer as timer;
use \apps\nf\models as models;
class deleted extends \apps\nf\controllers\_{
	function __construct() {
		parent::__construct();


	}
	function page() {
		$user = $this->f3->get("user");
		if (!$user['permissions']['records']['deleted']['page']) $this->f3->error(404);
		$userID = $user['ID'];
		$pID = $user['pID'];

		//test_array($user);
		$app_settings = \apps\nf\settings::_available();
		$settings = models\settings::_read("deleted");


		$a = array();
		$b = array();

		foreach ($settings['col'] as $col) {
			$a[] = $col;
			$b[] = $col['c'];

		}


		$selected = $a;
		$available = array();
		foreach ($app_settings["columns"] as $col) {
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
		$tmpl = new \template("template.tmpl","apps/nf/ui/");
		$tmpl->page = array(
			"section"=> "records",
			"sub_section"=> "deleted",
			"template"=> "deleted",
			"print"=> "/app/nf/records/deleted/print",
			"meta"    => array(
				"title"=> "NF - Deleted Records",
			)
		);


		$tmpl->date_range = json_encode($date_range);

		
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


		$tmpl = new \template("template.tmpl", "apps/nf/ui/print/",true);
		$tmpl->page = array(
			"section"    => "bookings",
			"sub_section"=> "deleted",
			"template"   => "deleted",
			"meta"       => array(
				"title"=> "NF - Print - Deleted",
			)
		);

		$tmpl->settings = $settings;
		$tmpl->data = $data;

		//test_array($data);

		$tmpl->output();
		$timer->stop("Controller - " . __CLASS__ . " - " . __FUNCTION__, func_get_args());
	}

}
