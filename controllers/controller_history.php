<?php
/*
 * Date: 2011/11/16
 * Time: 11:16 AM
 */
namespace controllers;
class controller_history {
	function __construct(){
		$this->f3 = \base::instance();
	}
	function page(){





		$tmpl = new \template("template.tmpl", "ui/front/", true);
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
