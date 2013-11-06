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
		$cmstyle =  $user['company']['nf_cm_css'];
		
		

		
		
		
		if (count($_POST)){
			$values = array(
				"nf_cm_css"=>clean_style((isset($_POST['cm-block-form'])?$_POST['cm-block-form']:NULL),true)
			);
			\models\company::save($cID,$values);
			$cmstyle = $values['nf_cm_css'];
			
		}

		if ($cmstyle=='' || $cmstyle ==NULL){
			$cmstyle = $cfg['nf']['default_cm_calc_css'];
		}

//test_array($cmstyle);

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
		
		
	

		

		
		$tmpl->cm_calc_css = clean_style($cmstyle);
		
		
		$tmpl->output();

	}

}
