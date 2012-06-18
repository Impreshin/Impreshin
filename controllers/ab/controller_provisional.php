<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace controllers\ab;
use \F3 as F3;
use \timer as timer;
use \models\ab as models;
use \models\user as user;
class controller_provisional {
	function __construct() {
		$user = F3::get("user");
		$userID = $user['ID'];
		if (!$userID) F3::reroute("/login");
		F3::get("DB")->exec("UPDATE global_users SET last_page = '" . $_SERVER['REQUEST_URI'] . "' WHERE ID = '" . $user['ID'] . "'");
		models\user_settings::save_config(array("page"=> $_SERVER['REQUEST_URI']));
	}
	function page() {
		$timer = new timer();
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];
		$currentDate = $user['publication']['current_date'];
		//test_array($user);
		$ab_settings = F3::get("settings");
		//test_array($ab_settings);






		$tmpl = new \template("template.tmpl","ui/adbooker/");
		$tmpl->page = array(
			"section"=> "bookings",
			"sub_section"=> "provisional",
			"template"=> "page_provisional",
			"meta"    => array(
				"title"=> "AdBooker - Bookings",
			)
		);


		$a = array();
		$b = array();
		if (isset($user['settings']['list']['col'])&& count($ab_settings["columns"])){

			foreach ($ab_settings["columns"] as $col){
				if ( !in_array($col['c'],$user["settings"]["list"]['col'])){
					$col["s"] = "0";
					$a[] = $col;
				}

			}




			foreach ($user["settings"]["list"]['col'] AS $col){
				$v = $ab_settings["columns"][$col];
				$v['s'] = '1';
				$b[] = $v;
			}

			$ab_settings[1] = array_merge($b,$a);




		}
		$ab_settings['list'] = $user['settings']['list'];
	//	test_array($ab_settings);
		$tmpl->production = models\production::getAll("pID='$pID'","production ASC");
		$tmpl->repeat_dates = models\dates::getAll("pID='$pID' AND publish_date >= '" . $currentDate['publish_date'] . "'", "publish_date ASC", "");

		$tmpl->settings = $ab_settings;

		$tmpl->settings_columns = array(
			"selected"=>$b,
			"available"=>$a
		);
		$tmpl->output();
		$timer->stop("Controller - ".__CLASS__." - ".__FUNCTION__, func_get_args());
	}


}
