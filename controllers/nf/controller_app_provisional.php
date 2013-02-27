<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace controllers\nf;
use \F3 as F3;
use \timer as timer;
use \models\nf as models;
use \models\user as user;
class controller_app_provisional {
	function __construct() {
		$this->f3 = \base::instance();
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		if (!$userID) $this->f3->reroute("/login");
	}
	function page() {

		$timer = new timer();
		$user = $this->f3->get("user");
		$cID = $user['company']['ID'];
		//$this->f3->get("DB")->exec("UPDATE global_users SET last_page = '" . $_SERVER['REQUEST_URI'] . "' WHERE ID = '" . $user['ID'] . "'");


		$authors = \models\user::getAll("nf_author = '1' AND nf = '1' AND cID = '$cID'");

		//test_array($authors);

		$userID = $user['ID'];
		$pID = $user['pID'];
		$currentDate = $user['publication']['current_date'];
		//test_array($user);
		$settings = models\settings::_read("provisional", $user['permissions']);


		$raw_settings = $this->f3->get("settings");


		//$settings = models\settings::_read("provisional",$user['permissions']);




		//test_array($settings);

		$tmpl = new \template("template.tmpl","ui/nf/");
		$tmpl->page = array(
			"section"=> "bookings",
			"sub_section"=> "provisional",
			"template"=> "page_app_provisional",
			"meta"    => array(
				"title"=> "NF - Provisional",
			)
		);

		$a = array();
		$b = array();

		foreach ($settings['col'] as $col) {
			$a[] = $col;
			$b[] = $col['c'];

		}


		$selected = $a;
		$available = array();
		foreach ($raw_settings["columns"] as $col) {
			if (!in_array($col['c'], $b)) {
				$available[] = $col;
			}

		}

		$tmpl->settings_columns = array(
			"selected"  => $selected,
			"available" => $available
		);

		$tmpl->authors = $authors;
		$tmpl->settings = $settings;
		$tmpl->stages = models\stages::getAll();


		$tmpl->output();
		$timer->stop("Controller - ".__CLASS__." - ".__FUNCTION__, func_get_args());
	}


}
