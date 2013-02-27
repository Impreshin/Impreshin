<?php
/*
 * Date: 2011/11/16
 * Time: 11:16 AM
 */
namespace controllers;
class controller_screenshots {
	function __construct(){
		$this->f3 = \base::instance();
	}
	function page(){
		$tmpl = new \template("template.tmpl", "ui/front/", true);
		$tmpl->page = array(
			"section"    => "screenshots",
			"sub_section"=> "form",
			"template"   => "page_screenshots",
			"meta"       => array(
				"title"=> "ScreenShots",
			)
		);
		$tmpl->output();

	}


}
