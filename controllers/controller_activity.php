<?php
/*
 * Date: 2011/11/16
 * Time: 11:16 AM
 */
namespace controllers;
class controller_activity {
	function __construct(){
		$this->f3 = \base::instance();
	}
	function page(){
		$tmpl = new \template("template.tmpl", "ui/front/", true);
		$tmpl->page = array(
			"section"     => "activity",
			"sub_section" => "",
			"template"    => "page_activity",
			"meta"        => array(
				"title" => "Activity",
			)
		);
		$tmpl->output();

	}
	function data(){
		$d = file_get_contents("https://github.com/Impreshin/Impreshin/graphs/commit-activity-data");
		header("Content-Type: application/json");
		echo $d;

		exit();
	}


}
