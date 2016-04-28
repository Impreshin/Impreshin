<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace  apps\nf\controllers;

use \timer as timer;
use \apps\nf\models as models;

class form extends \apps\nf\controllers\_{
	function __construct() {
		parent::__construct();
	}
	function page_new(){
//test_array($user);
		if ($this->user['permissions']['form']['new']) {
			$this->page();
		} else {
			$this->f3->error(404);
		}
		
		//test_array($this->user);

		//$this->page();
	}
	function page_edit(){
		//if ($this->user['permissions']['form']['edit'] || $this->user['permissions']['form']['edit_master'] || $this->user['permissions']['form']['delete']) {
			$user = $this->user;
			$uID = $this->user['ID'];
			$aID = $this->f3->get('PARAMS["ID"]');

		$b = new \DB\SQL\Mapper($this->f3->get("DB"), "nf_articles");
		$b->load("ID='$aID'");
		
		
		$stage = $b->stageID;
		if ($user['permissions']['stages'][$stage]['edit'] || $this->user['permissions']['form']['edit_master']){
			if (!$b->dry()){
				$b->locked_uID = $uID;
				$b->save();
			}
			$this->page();
		} else {
			
			if ($b->authorID == $user['ID']){
				echo "you do not have permission to edit this article any more";
			} else {
				echo "you dont have permission to view this page";
			}
			echo "<hr>";
			echo "<a href='/app/nf'>Click here to go back to the main list</a>";
			
			
			exit();
			
		}
	}

	function page(){
		$timer = new timer();
		$ID = $this->f3->get('PARAMS["ID"]');
		$user = $this->user;
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];

		$cfg = $this->f3->get("CFG");

		$settings = \apps\nf\settings::_available();

		
		
		
		$detailsO = new models\articles();
		$details = $detailsO->get($ID);
		//test_array($details);
		if ($details['ID'] && ($details['cID'] != $user['company']['ID'])) {
			$details = $detailsO->dbStructure();
		}
		
		
		$v = $this->f3->get("VERSION");

		//test_array(array("stages" => $stages, "current" => $current, "next" => $next));

		

		$users = \models\user::getAll("cID='$cID' AND nf_author='1'", "fullName ASC");

		$u = array();
		foreach ($users as $item){
			$item = array(
				"ID"=>$item['ID'],
				"fullName" => $item['fullName'],
			);
			$u[] = $item;
		}
		$users = $u;
		

		//test_array($users);
	

		$tmpl = new \template("template.tmpl", "apps/nf/ui/");
		$tmpl->page = array(
			"section"     => "form",
			"sub_section" => "form",
			"template"    => "form",
			"meta"        => array(
				"title" => "NF - Form - loading..",
			),
			"js"=>array("/ui/ckeditor/ckeditor.js?v={$v}","/ui/plupload/js/jquery.plupload.queue/jquery.plupload.queue.{$v}.js","/ui/fancybox/jquery.fancybox.{$v}.js","/ui/spellchecker/js/jquery.spellchecker.{$v}.js"),
			"css"=>array("/ui/plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.{$v}.css", "/ui/fancybox/jquery.fancybox.{$v}.css","/ui/spellchecker/css/jquery.spellchecker.{$v}.css"),
			//"help"        => "/apps/nf/help/form"
		);


		$tmpl->ID = $details['ID'];
		$tmpl->settings = $settings;
		$tmpl->uploadPath = $user['company']['ID']."/".date("Y")."/";
		
		$tmpl->types = models\types::getAll("","orderby ASC");
		$tmpl->categories = models\categories::getAll("cID='". $cID."'","orderby ASC");
		$tmpl->priorities = models\priorities::getAll("cID='". $cID."'","orderby ASC");
		$tmpl->resources = models\resources::getAll("cID='". $cID."'","orderby ASC");
		$tmpl->authors = $users;
		$tmpl->cm_calc_css = $cfg['nf']['default_cm_calc_css'] . $user['company']['nf_cm_css'];
		//$tmpl->stages = $stages;


		
			

		$tmpl->enable_spellcheck = function_exists('enchant_broker_init')? '1' : '0';
		
		$custom_dictionary = $cID . "/custom.dic";

		if (!file_exists("./uploads/dictionaries/$custom_dictionary")){
			$custom_dictionary = "";
			
		}
		$tmpl->custom_dictionary = $custom_dictionary;
		
		
		
		$tmpl->output();
		$timer->stop("Controller - ".__CLASS__." - ".__FUNCTION__, func_get_args());
	}



}
