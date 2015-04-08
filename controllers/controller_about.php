<?php
/*
 * Date: 2011/11/16
 * Time: 11:16 AM
 */
namespace controllers;
class controller_about {
	function __construct(){
		$this->f3 = \base::instance();
	}
	function page(){

		$username = isset($_POST['login_email']) ? $_POST['login_email'] : "";
		$password = isset($_POST['login_password']) ? $_POST['login_password'] : "";
		if (!$username) $username = isset($_COOKIE['username']) ? $_COOKIE['username'] : "";
		
		$apps = $this->f3->get("applications");
		//test_array($apps); 


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

		
		
		



		$tmpl = new \template("template.tmpl", "ui/front/", true);
		$tmpl->page = array(
			"section"     => "about",
			"sub_section" => "",
			"template"    => "page_about",
			"meta"        => array(
				"title" => "Welcome to Impreshin",
			),
			"css"=>"/ui/_css/plugins/ticker-style.css",
			"js"=>"/ui/_js/plugins/jquery.ticker.js"
		);
		$tmpl->username = $username ;
		$tmpl->apps = $apps ;
		$tmpl->news_items = json_encode($d) ;
		$tmpl->output();

	}


}
