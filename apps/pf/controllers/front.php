<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace apps\pf\controllers;
use \timer as timer;
use \apps\pf\models as models;
use \models\user as user;
use \models\dates as dates;
class front extends \apps\pf\controllers\_ {
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
		$app_settings = \apps\pf\settings::_available();



		//test_array($user);


		$settings = models\settings::_read("front",$user['permissions']);


		//test_array($settings);

		//test_array($settings);

		$tmpl = new \template("template.tmpl","apps/pf/ui/");
		$tmpl->page = array(
			"section"=> "front",
			"sub_section"=> "",
			"template"=> "front",
			
			"meta"    => array(
				"title"=> "PF - Front",
			),
			//"help"=> "/apps/nf/help/bookings"
		);


		




		$tmpl->settings = $settings;
		$tmpl->use_pub = true;

		
		$tmpl->output();
		$timer->stop("Controller - ".__CLASS__." - ".__FUNCTION__, func_get_args());
	}

	


}
