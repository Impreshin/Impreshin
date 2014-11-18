<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace apps\cm\controllers;
use \timer as timer;
use \apps\cm\models as models;
use \models\user as user;
use \models\dates as dates;
class companies extends \apps\cm\controllers\_ {
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
		$app_settings = \apps\cm\settings::_available("","companies");
		
		


		//test_array($app_settings);


		$settings = models\settings::_read("companies","companies");


		//test_array($settings);

		//test_array($settings);

		$tmpl = new \template("template.tmpl","apps/cm/ui/");
		$tmpl->page = array(
			"section"=> "companies",
			"sub_section"=> "",
			"template"=> "companies",
			
			"meta"    => array(
				"title"=> "CM - Companies",
			),
			//"help"=> "/apps/nf/help/bookings"
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





		$tmpl->settings_columns = array(
			"selected"=> $selected,
			"available"=> $available
		);




		$tmpl->settings = $settings;
		$tmpl->use_pub = false;
		$tmpl->activity_range = json_encode($app_settings['general']['activity_range']);

		
		$tmpl->output();
		$timer->stop("Controller - ".__CLASS__." - ".__FUNCTION__, func_get_args());
	}

	


}
