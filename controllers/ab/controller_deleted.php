<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace controllers\ab;
use \F3 as F3;
use \timer as timer;
use models\ab as models;
class controller_deleted {
	function __construct() {
		$user = F3::get("user");
		$userID = $user['ID'];
		if (!$userID) F3::reroute("/login");

		F3::get("DB")->exec("UPDATE global_users SET last_page = '" . $_SERVER['REQUEST_URI'] . "' WHERE ID = '" . $user['ID'] . "'");

	}
	function page() {
		$user = F3::get("user");
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
		$tmpl = new \template("template.tmpl","ui/adbooker/");
		$tmpl->page = array(
			"section"=> "records",
			"sub_section"=> "deleted",
			"template"=> "page_deleted",
			"meta"    => array(
				"title"=> "AdBooker - Deleted Bookings",
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

}
