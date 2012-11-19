<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace controllers\nf;
use \F3 as F3;
use \timer as timer;
use models\nf as models;

class controller_app_form {
	function __construct() {
	}

	function page() {
		$ID = F3::get('PARAMS["ID"]');
		$user = F3::get("user");

		$settings = models\settings::_read("form");





//test_array($user);

		$detailsO = new models\articles();
		$details = $detailsO->get($ID);
		//test_array($details);
		if ($details['ID']=="" || $details['deleted']=='1'){
			$details =$detailsO->dbStructure();
		}


		$title = "New Article";
		if ($details['ID']){
			$title = "Edit Article";
		}

		$tmpl = new \template("template.tmpl", "ui/nf/");
		$tmpl->page = array(
			"section"    => "form",
			"sub_section"=> "form",
			"template"   => "page_app_form",
			"meta"       => array(
				"title"=> "NF - Form - $title",
			),
			"help"=>"/nf/help/form"
		);



		$tmpl->settings  = $settings;



		$tmpl->output();

	}


}
