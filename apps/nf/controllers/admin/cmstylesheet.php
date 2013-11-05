<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace apps\nf\controllers\admin;

use \timer as timer;
use \apps\nf\models as models;
class cmstylesheet extends \apps\nf\controllers\_ {
	function __construct() {
		parent::__construct();
	}
	function page() {
		$user = $this->f3->get("user");
		$userID = $user['ID'];

		$cID = $user['company']['ID'];

		$cfg = $this->f3->get("CFG");

//test_array($pages);

		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","apps/nf/ui/");
		$tmpl->page = array(
			"section"=> "admin",
			"sub_section"=> "cmstylesheet",
			"template"=> "admin_cmstylesheet",
			"meta"    => array(
				"title"=> "NF - Admin - Cm Style SHeet",
			),
			"js"=>array("/ui/ckeditor/ckeditor.js"),
			"css"=>array(),
		);
		
		$cmstyle = $cfg['nf']['default_cm_calc_css'] . $user['company']['nf_cm_css'];

		$cmstyle = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($cmstyle));
		
		$cmstyle = str_replace(";",";&#10;    ",$cmstyle);
		$cmstyle = str_replace("}","}&#10;&#10;",$cmstyle);
		$cmstyle = str_replace("{","{&#10;    ",$cmstyle);
		$cmstyle = str_replace("&#10;    }","&#10;}",$cmstyle);
		$cmstyle = str_replace("     ","    ",$cmstyle);

		
		$tmpl->cm_calc_css = $cmstyle;
		
		
		$tmpl->output();

	}

}
