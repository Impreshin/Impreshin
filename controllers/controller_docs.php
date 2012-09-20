<?php
/*
 * Date: 2011/11/16
 * Time: 11:16 AM
 */
namespace controllers;
class controller_docs {
	function __construct(){

	}
	function page(){






		$tmpl = new \template("template.tmpl", array("ui/front/","docs/templates/"), true);
		$tmpl->page = array(
			"section"    => "docs",
			"sub_section"=> "home",
			"template"   => "page_docs_home",
			"meta"       => array(
				"title"=> "Documentation",
			)
		);


		$tmpl->output();

	}



}
