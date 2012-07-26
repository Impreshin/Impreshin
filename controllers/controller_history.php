<?php
/*
 * Date: 2011/11/16
 * Time: 11:16 AM
 */
namespace controllers;
class controller_history {
	function __construct(){

	}
	function page(){
		$tmpl = new \template("template.tmpl", "ui/front/");
		$tmpl->page = array(
			"section"    => "history",
			"sub_section"=> "form",
			"template"   => "page_history",
			"meta"       => array(
				"title"=> "History",
			)
		);
		$tmpl->output();

	}


}
