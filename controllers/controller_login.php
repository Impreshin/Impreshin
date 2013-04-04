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


		$path = "./uploads/news/";
		$dates = glob($path . "*", GLOB_ONLYDIR);
		$end = strtotime('today');

		$d = array();
		foreach ($dates as $item) {
			$folder = str_replace(array($path), "", $item);
			$date = str_replace(array("_"), "-", $folder);
			$date_raw = $date;
			$date = strtotime($date);



			$days_between = ceil(abs($end - $date) / 86400);

			if ($days_between < 30){
				$d[] = array(
					"ID"       => "",
					"datein"   => date("Y-m-d", $date),
					"datein_d" => date("d M Y", $date),
					"link"     => "/news/" . $folder,
					"news"     => "News item: " . timesince($date_raw) . " <span class='g'> (" . date("d F Y", $date) . ")</span>",
					"ticker"   => "1",
					"userID"   => "",
					"days_ago"   => $days_between,

				);

			}


		}
		$d = array_reverse($d);
		

		
		
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
		$tmpl->news_items = json_encode($d) ;
		$tmpl->output();

	}


}
