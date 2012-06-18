<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace controllers\ab;
use \F3 as F3;
use \timer as timer;
use models\ab as models;
class controller_search {
	function __construct() {
		$user = F3::get("user");
		$userID = $user['ID'];
		if (!$userID) F3::reroute("/login");

		F3::get("DB")->exec("UPDATE global_users SET last_page = '". $_SERVER['REQUEST_URI']."' WHERE ID = '" . $user['ID'] . "'");
		models\user_settings::save_config(array("page"=> $_SERVER['REQUEST_URI']));

	}
	function page() {
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];

		//test_array($user);
		$ab_settings = F3::get("settings");
		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","ui/adbooker/");
		$tmpl->page = array(
			"section"=> "records",
			"sub_section"=> "search",
			"template"=> "page_search",
			"meta"    => array(
				"title"=> "AdBooker - Search",
			)
		);



		$a = array();
		$b = array();
		if (isset($user['settings']['search']['col'])&& count($ab_settings["columns"])){

			foreach ($ab_settings["columns"] as $col){
				if ( !in_array($col['c'],$user["settings"]["search"]['col'])){
					$col["s"] = "0";
					$a[] = $col;
				}

			}


			foreach ($user["settings"]["search"]['col'] AS $col){
				$v = $ab_settings["columns"][$col];
				$v['s'] = '1';
				$b[] = $v;
			}

			$ab_settings[1] = array_merge($b,$a);




		}
		//foreach ($ab_settings[''])

		$dates = models\dates::getAll("pID='$pID' AND publish_date <= '" . $user['publication']['current_date']['publish_date'] . "'", "publish_date DESC", "0,5");

	//test_array($dates);



		$tmpl->production = models\production::getAll("pID='$pID'","production ASC");
		$tmpl->repeat_dates = models\dates::getAll("pID='$pID' AND publish_date >= '" . $user['publication']['current_date']['publish_date'] . "'", "publish_date ASC", "");
		$tmpl->dates = $dates;


		$ab_settings['list'] = $user['settings']['search'];

	//	test_array($ab_settings);

		$date_range = F3::get("DB")->exec("SELECT min(publish_date) as earliestDate, max(publish_date) as latestDate FROM global_dates WHERE pID = '$pID'");
		if (count($date_range)){
			$date_range = $date_range[0];
		}

		$tmpl->date_range = json_encode($date_range);

		$tmpl->settings = $ab_settings;




		$tmpl->settings_columns = array(
			"selected"=>$b,
			"available"=>$a
		);
		$tmpl->output();

	}

}
