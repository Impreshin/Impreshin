<?php
/*
 * Date: 2011/11/16
 * Time: 11:16 AM
 */

namespace controllers;
use \F3 as F3;
use \Jig as Jig;
use \FileDB as FileDB;
class controller_docs {
	private $raw;
	function __construct(){
		$app = F3::get('PARAMS.app');
		$section = F3::get('PARAMS.section');
		$sub_section = F3::get('PARAMS.sub_section');
		$sub_section_item = F3::get('PARAMS.item');

		$this->data = $this->get($app, $section, $sub_section, $sub_section_item);


		$this->setup = array(
			"app"=>$app,
			"section"=>$section,
			"sub_section"=>$sub_section,
			"sub_section_item"=>$sub_section_item
		);

		//test_array(array($this->setup, $this->data));

	}
	function filter($data=array()){
		$return = array();
		$data = (array)$data;


		foreach ($data as $k => $i){
			if (isset($i) && $i && is_array($i)){
				if (isset($i['p']) && $i['p'] == '0') {
					unset($data[$k]);
				}
				foreach ($i as $kk => $ii) {
					if (isset($ii['p']) && $ii['p'] == '0'){
						unset($data[$k][$kk]);
						if (!count($data[$k])) unset($data[$k]);
					}
					if (isset($ii) && $ii && is_array($ii)) {
						foreach ($ii as $kkk => $iii) {
							if (isset($iii['p']) && $iii['p'] == '0') {
								unset($data[$k][$kk][$kkk]);
								if (!count($data[$k][$kk])) unset($data[$k][$kk]);
							}
							if (isset($iii) && $iii && is_array($iii)) {
								foreach ($iii as $kkkk => $iiii) {
									if (isset($iiii['p']) && $iiii['p'] == '0') {
										unset($data[$k][$kk][$kkk][$kkkk]);
										if (!count($data[$k][$kk][$kkk])) unset($data[$k][$kk][$kkk]);

									}

									if (isset($iiii) && $iiii && is_array($iiii)) {
										foreach ($iiii as $kkkkk => $iiiii) {
											if (isset($iiiii['p']) && $iiiii['p'] == '0') {
												unset($data[$k][$kk][$kkk][$kkkk][$kkkkk]);
												if (!count($data[$k][$kk][$kkk][$kkkk])) unset($data[$k][$kk][$kkk][$kkkk]);

											}
											if (isset($iiiii) && $iiiii && is_array($iiiii)) {
												foreach ($iiiii as $kkkkkk => $iiiiii) {
													if (isset($iiiiii['p']) && $iiiiii['p'] == '0') {
														unset($data[$k][$kk][$kkk][$kkkk][$kkkkk][$kkkkkk]);
														if (!count($data[$k][$kk][$kkk][$kkkk][$kkkkk])) unset($data[$k][$kk][$kkk][$kkkk][$kkkkk]);

													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}



		return $data;
	}
	function get($app="", $section="", $sub_section="", $sub_section_item=""){
		$data = F3::get('docs');
		$data = $this->filter($data);



		$t = array(
			"app"             => $app,
			"section"         => $section,
			"sub_section"     => $sub_section,
			"sub_section_item"=> $sub_section_item,
			"details"=>($sub_section_item)?true:false
		);
		if ($sub_section){
			if (!isset($data[$app][$section]['help'][$sub_section]['help'])) {
				$t['details'] = true;
			}
		}

		$this->raw = $t + array("data"=>$data);
		//test_array($t + $data[$app][$section]['help'][$sub_section]);






		if ($app) {
			if (isset($data[$app])) {
				$data = $data[$app];
			} else {
				F3::error(404);
			}

		}

		$title = "";
		$sub_title = "";
		$file = "";
		if ($section){
			if (isset($data[$section])) {
				$data = $data[$section];
				$title = $data['title'];
				$file = $data['file'];
				unset($data['title']);
				if ($sub_section || $sub_section=='0') {
					if (isset($data['help'][$sub_section])) {
						$data = $data['help'][$sub_section];
						$sub_title = (isset($data['title']))? $data['title']: $data['heading'];
						$file = $data['file'];
						if ($sub_section_item) {
							if (isset($data['help'][$sub_section_item])) {
								$data = $data['help'][$sub_section_item];
							} else {
								F3::reroute("/$app/help/$section/$sub_section");
							}
						}
					} else {
						F3::reroute("/$app/help/$section");
					}
				}

			} else {
				//$data = $data[$app];
				F3::reroute("/$app/help");
			}
		}
		if (!count($data))F3::error(404);

		$return = array();
		if (isset($data['help'])) $data = $data['help'];
		$return['help']= $data;
		$return['title']= $title;
		$return['sub_title']= $sub_title;
		$return['file']= $file;

		$return = $t + $return;

		if (isset($_GET['t'])) test_array($return);

		return $return;
	}
	function help_page(){
		$data = $this->data;
		$tmpl = new \template("template.tmpl", array("docs/templates/"));
		$tmpl->page = array(
			"section"    => "docs",
			"sub_section"=> "home",
			"template"   => "page_docs_home",
			"meta"       => array(
				"title"=> "Documentation",
			)
		);

		$tmpl->data = $this->data;;

		$tmpl->output();

	}
	function section_page(){
		$data = $this->data;
		$raw = $this->raw;
		//test_array($raw);

		$tmpl = new \template("template.tmpl", array("docs/templates/","ui/","ui/".$data['app']."/"));
		$tmpl->page = array(
			"section"    => "docs",
			"sub_section"=> "home",
			"template"   => "page_docs_section",
			"meta"       => array(
				"title"=> "Documentation - ". $data['title'],
			)
		);

		$tmpl->data = $data;
		$tmpl->raw = $raw;
		$tmpl->output();

	}
	function sub_section_page(){
		$data = $this->data;
		$raw = $this->raw;

		//test_array($raw);
		if ($data['details']){
			$this->sub_section_item_page();
		} else {
			$tmpl = new \template("template.tmpl", array("docs/templates/",	"ui/","ui/" . $data['app'] . "/"
			));
			$tmpl->page = array(
				"section"    => "docs",
				"sub_section"=> "home",
				"template"   => "page_docs_sub_section",
				"meta"       => array(
					"title"=> "Documentation - " . $data['title'] . " - ". $data['sub_title'],
				)
			);

			$tmpl->data = $data;
			$tmpl->raw = $raw;

			$tmpl->output();
		}




	}
	function sub_section_item_page(){
		$data = $this->data;
		$raw = $this->raw;


		$title = "Documentation - " . $data['title'] . " - " . $data['sub_title'] . " - " . $data['help']['heading'];
		if ($data['details'] && $data['sub_section_item']==''){
			$title = "Documentation - " . $data['title'] . " - " . $data['sub_title'];
		}

		$tmpl = new \template("template.tmpl", array("docs/templates/"));
		$tmpl->page = array(
			"section"    => "docs",
			"sub_section"=> "home",
			"template"   => "page_docs_sub_section_item",
			"meta"       => array(
				"title"=> $title,
			)
		);

		$tmpl->data = $data;
		$tmpl->raw = $raw;

		$tmpl->output();

	}




}
