<?php
/*
 * Date: 2011/11/16
 * Time: 11:16 AM
 */
namespace controllers;
class controller_about {
	function __construct(){

	}
	function page(){
		$tmpl = new \template("template.tmpl", "ui/front/");
		$tmpl->page = array(
			"section"    => "about",
			"template"   => "page_about",
			"meta"       => array(
				"title"=> "About",
			)
		);
		$tmpl->output();

	}


}