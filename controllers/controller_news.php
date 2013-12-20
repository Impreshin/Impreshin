<?php
/*
 * Date: 2011/11/16
 * Time: 11:16 AM
 */
namespace controllers;
class controller_news {
	function __construct(){
		$this->f3 = \base::instance();
	}
	function page(){
		$selected_folder = $this->f3->get('PARAMS["item"]');

		$path = "./front/news/";
		$dates = glob($path."*", GLOB_ONLYDIR);


		$d = array();
		foreach ($dates as $item) {
			$folder = str_replace(array($path), "", $item);
			$date = str_replace(array("_"), "-", $folder);

			$d[] = array(
				"date"    => date("d M Y", strtotime($date)),
				"folder"  => $folder,
				"selected"=>($selected_folder == $folder)?1:0
			);

		}
		$d = array_reverse($d);

		if (!$selected_folder){
			$d[0]['selected'] = '1';
			$selected_folder = $d[0]['folder'];
		}
		$date = date("d F Y", strtotime(str_replace(array("_"), "-", $selected_folder)));


		$selected_folder = $path . $selected_folder;
		$item = (file_exists($selected_folder . DIRECTORY_SEPARATOR . "index.html")) ? file_get_contents($selected_folder . DIRECTORY_SEPARATOR . "index.html") : "";


		//test_array($item);

		$tmpl = new \template("template.tmpl", "ui/front/", true);
		$tmpl->page = array(
			"section"    => "news",
			"sub_section"=> "",
			"template"   => "page_news",
			"meta"       => array(
				"title"=> "News",
			)
		);
		$tmpl->items = $d;
		$tmpl->item = $item;
		$tmpl->date = $date;
		$tmpl->output();

	}



}
