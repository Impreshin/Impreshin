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


		$username = isset($_POST['login_email']) ? $_POST['login_email'] : "";
		$password = isset($_POST['login_password']) ? $_POST['login_password'] : "";
		if (!$username) $username = isset($_COOKIE['username']) ? $_COOKIE['username'] : "";


		$tmpl = new \template("template.tmpl", "ui/front/", true);
		$tmpl->page = array(
			"section"    => "history",
			"sub_section"=> "form",
			"template"   => "page_history",
			"meta"       => array(
				"title"=> "Impreshin | History",
			),
			"js" => "/ui/_js/plugins/FeedEk.js"
		);
		$tmpl->username = $username ;
		$tmpl->output();

	}



}
