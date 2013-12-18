<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace apps\nf\controllers\reports;

use \timer as timer;
use \apps\nf\models as models;
class publication_figures extends \apps\nf\controllers\_ {
	function __construct() {
		parent::__construct();
	}
	function page() {
		$user = $this->f3->get("user");
		$uID = $user['ID'];
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];

		$section = "reports_publication_figures";
		$settings = models\settings::_read($section);
		$settings_pub = isset($settings["pub_$pID"])?$settings["pub_$pID"]:array("pubs"=>"");

		$app_settings = \apps\nf\settings::_available();
		



		$users = \models\user::getAll("cID = '$cID' AND nf_author ='1'", "fullName ASC");
		
		
		
		
	

		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","apps/nf/ui/");
		$tmpl->page = array(
			"section"=> "reports",
			"sub_section"=> "publication_figures",
			"template"=> "reports_publication_figures",
			"meta"    => array(
				"title"=> "NF - Reports - Publication",
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




		$tmpl->settings = $settings;

		$tmpl->settings_columns = array(
			"selected"=> $selected,
			"available"=> $available
		);

		$tmpl->selected = (isset($settings['ID']["cID_$cID"])) ? $settings['ID']["cID_$cID"] : "";;

		$tmpl->users = $users;

		$tmpl->use_pub = true;

		$tmpl->settings = $settings;
		$tmpl->output();

	}

}
