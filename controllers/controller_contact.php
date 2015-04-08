<?php

namespace controllers;
class controller_contact {
	function __construct(){
		$this->f3 = \base::instance();
	}
	function page(){

		$username = isset($_POST['login_email']) ? $_POST['login_email'] : "";
		$password = isset($_POST['login_password']) ? $_POST['login_password'] : "";
		if (!$username) $username = isset($_COOKIE['username']) ? $_COOKIE['username'] : "";
		
		$apps = $this->f3->get("applications");
		//test_array($apps); 


		

		
		
		



		$tmpl = new \template("template.tmpl", "ui/front/", true);
		$tmpl->page = array(
			"section"     => "contact",
			"sub_section" => "",
			"template"    => "page_contact",
			"meta"        => array(
				"title" => "Impreshin | Contact",
			),
			
		);
		$tmpl->username = $username ;
		$tmpl->apps = $apps ;
		$tmpl->output();

	}


}
