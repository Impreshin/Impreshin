<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace controllers\ab;
use \F3 as F3;
use \timer as timer;
use models\ab as models;
class controller_app_search {
	function __construct() {

		$this->f3 = \base::instance();

	}
	function page() {
		$user = $this->f3->get("user");
		if (!$user['permissions']['records']['search']['page']) $this->f3->error(404);
		$userID = $user['ID'];
		$pID = $user['pID'];

		//test_array($user);
		$ab_settings = $this->f3->get("settings");
		//test_array($ab_settings);


		$settings = models\settings::_read("search");


			//test_array($settings);

		$tmpl = new \template("template.tmpl","ui/ab/");
		$tmpl->page = array(
			"section"=> "records",
			"sub_section"=> "search",
			"template"=> "page_app_search",
			"print"=> "/ab/print/search",
			"meta"    => array(
				"title"=> "AB - Search",
			)
		);


		$a = array();
		$b = array();

		foreach ($settings['col'] as $col){
			$a[] = $col;
			$b[] = $col['c'];

		}



		$selected = $a;
		$available = array();
			foreach ($ab_settings["columns"] as $col){
				if ( !in_array($col['c'],$b)){
					$available[] = $col;
				}

			}

		$dates = models\dates::getAll("pID='$pID' AND publish_date <= '" . $user['publication']['current_date']['publish_date'] . "'", "publish_date DESC", "0,5");

	//test_array($dates);



		$tmpl->production = models\production::getAll("pID='$pID'","production ASC");
		$tmpl->repeat_dates = models\dates::getAll("pID='$pID' AND publish_date >= '" . $user['publication']['current_date']['publish_date'] . "'", "publish_date ASC", "");
		$tmpl->dates = $dates;



		$date_range = $this->f3->get("DB")->exec("SELECT min(publish_date) as earliestDate, max(publish_date) as latestDate FROM global_dates WHERE pID = '$pID'");
		if (count($date_range)){
			$date_range = $date_range[0];
		}

		$tmpl->date_range = json_encode($date_range);

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
		if (!$user['permissions']['records']['search']['page']) $this->f3->error(404);
		$settings = models\settings::_read("search", $user['permissions']);


		$dataO = new \controllers\ab\data\search();
		$data = $dataO->_list(true);



		$tmpl = new \template("template.tmpl", "ui/ab/print/",true);
		$tmpl->page = array(
			"section"    => "bookings",
			"sub_section"=> "search",
			"template"   => "page_app_search",
			"meta"       => array(
				"title"=> "AB - Print - Search",
			)
		);

		$tmpl->settings = $settings;
		$tmpl->data = $data;

		//test_array($data);

		$tmpl->output();
		$timer->stop("Controller - " . __CLASS__ . " - " . __FUNCTION__, func_get_args());
	}

}
