<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace apps\nf\controllers;
use \timer as timer;
use \apps\nf\models as models;
use \models\user as user;
use \models\dates as dates;
class production extends \apps\nf\controllers\_ {
	function __construct() {
		parent::__construct();
	}
	function page() {

		$timer = new timer();
		$user = $this->user;
		//$this->f3->get("DB")->exec("UPDATE global_users SET last_page = '" . $_SERVER['REQUEST_URI'] . "' WHERE ID = '" . $user['ID'] . "'");


//test_array($user);

		$userID = $user['ID'];
		$pID = $user['pID'];
		//test_array($user);
		$app_settings = \apps\nf\settings::_available();
		$stages = models\stages::getAll("cID='". $user['company']['ID']."' OR cID='0'","orderby ASC");



		//test_array($user);


		$settings = models\settings::_read("production",$user['permissions']);


		//test_array($settings);

		//test_array($settings);

		$tmpl = new \template("template.tmpl","apps/nf/ui/");
		$tmpl->page = array(
			"section"=> "production",
			"sub_section"=> "list",
			"template"=> "production",
			"print"=> "/app/nf/production/print",
			"meta"    => array(
				"title"=> "NF - Production",
			),
			//"help"=> "/apps/nf/help/production"
		);


		$a = array();
		$b = array();

		foreach ($settings['col'] as $col){
			$a[] = $col;
			$b[] = $col['c'];

		}



		$selected = $a;
		$available = array();
			foreach ($app_settings["columns"] as $col){
				if ( !in_array($col['c'],$b)){
					$available[] = $col;
				}

			}




		$tmpl->settings = $settings;
		$tmpl->stages = $stages;
		$tmpl->use_pub = true;

		$tmpl->settings_columns = array(
			"selected"=> $selected,
			"available"=> $available
		);
		$tmpl->output();
		$timer->stop("Controller - ".__CLASS__." - ".__FUNCTION__, func_get_args());
	}

	function _print() {
		$timer = new timer();
		$user = $this->f3->get("user");

		$settings = models\settings::_read("provisional", $user['permissions']);


		$dataO = new \apps\nf\controllers\data\provisional();
		$data = $dataO->_list();

		//test_array($data);

		$tmpl = new \template("template.tmpl","apps/nf/ui/print/",true);
		$tmpl->page = array(
			"section"=> "bookings",
			"sub_section"=> "provisional",
			"template"=> "page_app_provisional",
			"meta"    => array(
				"title"=> "AB - Print - Provisional",
			)
		);

		$tmpl->settings=$settings;
		$tmpl->data=$data;

		//test_array($data);

		$tmpl->output();
		$timer->stop("Controller - ".__CLASS__." - ".__FUNCTION__, func_get_args());
	}


}
