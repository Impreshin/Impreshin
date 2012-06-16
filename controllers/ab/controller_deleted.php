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
		//if (!$userID) F3::reroute("/login");


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
			"sub_section"=> "deleted",
			"template"=> "page_deleted",
			"meta"    => array(
				"title"=> "AdBooker - Deleted Bookings",
			)
		);



		$a = array();
		$b = array();
		if (isset($user['settings']['deleted']['col'])&& count($ab_settings["columns"])){

			foreach ($ab_settings["columns"] as $col){
				if ( !in_array($col['c'],$user["settings"]["deleted"]['col'])){
					$col["s"] = "0";
					$a[] = $col;
				}

			}


			foreach ($user["settings"]["deleted"]['col'] AS $col){
				$v = $ab_settings["columns"][$col];
				$v['s'] = '1';
				$b[] = $v;
			}

			$ab_settings[1] = array_merge($b,$a);




		}
		//foreach ($ab_settings[''])

		$dates = models\dates::getAll("pID='$pID' AND publish_date <= '" . $user['publication']['current_date']['publish_date'] . "'", "publish_date DESC", "0,5");

	//test_array($ab_settings);



		$tmpl->production = models\production::getAll("pID='$pID'","production ASC");
		$tmpl->dates = $dates;


		$ab_settings['list'] = $user['settings']['deleted'];

	//	test_array($ab_settings);


		$tmpl->settings = $ab_settings;

		$tmpl->settings_columns = array(
			"selected"=>$b,
			"available"=>$a
		);
		$tmpl->output();

	}

}
