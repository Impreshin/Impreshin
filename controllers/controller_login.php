<?php
/*
 * Date: 2011/11/16
 * Time: 11:16 AM
 */
namespace controllers;
use \F3 as F3;
class controller_login {
	function __construct(){
		$this->f3 = \base::instance();
	}
	function page(){
		$user = $this->f3->get("user");
		$tmpl = new \template("template.tmpl", "ui/front/", true);
		$tmpl->page = array(
			"section"    => "login",
			"sub_section"=> "form",
			"template"   => "page_login",
			"meta"       => array(
				"title"=> "Login",
			)
		);

		$username = isset($_POST['login_email']) ? $_POST['login_email'] : "";
		$password = isset($_POST['login_password']) ? $_POST['login_password'] : "";

		$msg = "Please Login";
		if (($username && $password) ){
			if (isset($user['ID']) && $user['ID']) $this->f3->reroute("/?to=".(isset($_GET['to'])?$_GET['to']:''));
			$msg = "Login Failed";
		}
		if (!$username) $username = isset($_COOKIE['username']) ? $_COOKIE['username'] : "";

		if (isset($user['ID'])&&$user['ID']) $msg = "Already logged in";

		$tmpl->msg = $msg;
		$tmpl->user = $user;
		$tmpl->username = $username ;
		$tmpl->output();

	}


}
