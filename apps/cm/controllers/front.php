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
class front extends \apps\cm\controllers\_ {
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
		$app_settings = \apps\cm\settings::_available();



		//test_array($user);


		$settings = models\settings::_read("form",$user['permissions']);


		//test_array($settings);

		//test_array($settings);

		$tmpl = new \template("template.tmpl","apps/cm/ui/");
		$tmpl->page = array(
			"section"=> "front",
			"sub_section"=> "",
			"template"=> "front",
			
			"meta"    => array(
				"title"=> "CM - Front",
			),
			//"help"=> "/apps/nf/help/bookings"
		);


		




		$tmpl->settings = $settings;
		$tmpl->use_pub = false;

		
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
