<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace apps\ab\controllers;

use \timer as timer;
use \apps\ab\models as models;
use \models\user as user;
class controller_app_details extends \apps\ab\controllers\_ {
	function __construct() {
		parent::__construct();
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		if (!$userID) $this->f3->reroute("/login");
	}


	function _print() {
		$timer = new timer();
		$user = $this->f3->get("user");


		$dataO = new \apps\ab\controllers\data\data();
		$data = $dataO->details();

		//test_array($data);

		$tmpl = new \template("template.tmpl","apps/ab/ui/print/",true);
		$tmpl->page = array(
			"section"=> "bookings",
			"sub_section"=> "provisional",
			"template"=> "page_app_details",
			"meta"    => array(
				"title"=> "AB - Print - ".$data['client'],
			)
		);

		$tmpl->data=$data;

		//test_array($data);

		$tmpl->output();
		$timer->stop("Controller - ".__CLASS__." - ".__FUNCTION__, func_get_args());
	}


}
