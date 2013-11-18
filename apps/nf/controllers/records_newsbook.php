<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace apps\nf\controllers;

use \timer as timer;
use \apps\nf\models as models;
class records_newsbook extends \apps\nf\controllers\_ {
	function __construct() {

		parent::__construct();

	}
	function page() {
		$user = $this->f3->get("user");
		if (!$user['permissions']['records']['search']['page']) $this->f3->error(404);
		$userID = $user['ID'];
		$pID = $user['pID'];

		//test_array($user);
		$app_settings = \apps\nf\settings::_available();
		$settings = models\settings::_read("records_newsbook");


			//test_array($settings);

		$tmpl = new \template("template.tmpl","apps/nf/ui/");
		$tmpl->page = array(
			"section"=> "records",
			"sub_section"=> "records_newsbook",
			"template"=> "records_newsbook",
			//"print"=> "/app/nf/records/search/print",
			"meta"    => array(
				"title"=> "NF - Records Newsbook",
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
			foreach ($app_settings["columns"] as $col){
				if ( !in_array($col['c'],$b)){
					$available[] = $col;
				}

			}

	

	

		$selected_date = isset($settings['dID']["p_".$pID]) && $settings['dID']["p_".$pID] ? $settings['dID']["p_".$pID] : $user['publication']['current_date']['ID'];

	//test_array($selected);



	$date_list = \models\dates::getAll("pID='$pID'");
		//test_array($date_list);

		$tmpl->settings = $settings;
		
	
		

		$tmpl->date_list = $date_list;
		$tmpl->selected = $selected_date;


		$tmpl->use_pub = true;
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


		$dataO = new \apps\ab\controllers\data\search();
		$data = $dataO->_list(true);



		$tmpl = new \template("template.tmpl", "apps/ab/ui/print/",true);
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
