<?php
/*
 * Date: 2011/11/16
 * Time: 11:16 AM
 */
namespace controllers;
use \F3 as F3;
class controller_login {
	function __construct(){

	}
	function page(){
		$user = F3::get("user");
		$tmpl = new \template("template.tmpl", "ui/");
		$tmpl->page = array(
			"section"    => "login",
			"sub_section"=> "form",
			"template"   => "page_login",
			"meta"       => array(
				"title"=> "Login",
			)
		);

		$username = isset($_POST['email']) ? $_POST['email'] : "";
		$password = isset($_POST['password']) ? $_POST['password'] : "";

		$msg = "Please Login";
		if (($username && $password) ){
			if (isset($user['ID']) && $user['ID']) F3::reroute("/");
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
