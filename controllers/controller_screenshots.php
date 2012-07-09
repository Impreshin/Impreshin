<?php
/*
 * Date: 2011/11/16
 * Time: 11:16 AM
 */
namespace controllers;
class controller_screenshots {
	function __construct(){

	}
	function page(){
		$tmpl = new \template("template.tmpl", "ui/front/");
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
