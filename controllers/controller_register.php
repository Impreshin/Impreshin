<?php
/*
 * Date: 2011/11/16
 * Time: 11:16 AM
 */
namespace controllers;
class controller_register {
	function __construct(){

	}
	function page(){
		$tmpl = new \template("template.tmpl", "ui/");
		$tmpl->page = array(
			"section"    => "register",
			"sub_section"=> "form",
			"template"   => "page_register",
			"meta"       => array(
				"title"=> "Register",
			)
		);
		$tmpl->output();

	}


}
