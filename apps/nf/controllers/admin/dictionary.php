<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace apps\nf\controllers\admin;

use \timer as timer;
use \apps\nf\models as models;
class dictionary extends \apps\nf\controllers\_  {
	function __construct() {
		parent::__construct();
	}
	function page() {
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];

		$cfg = $this->f3->get("CFG");
		$folder = $cfg['upload']['folder'] . "/dictionaries/$cID/";
	
		
		
		
		$file = ($folder . "custom.dic");
		$file = $this->f3->fixslashes($file);
		//$file = ("./uploads/dictionaries/$cID/new_dic.dic");
		
		if (count($_POST)){
			$form = isset($_POST['words'])?$_POST['words']:array();
			$new_list = array();
			foreach ($form as $word){
				if ($word) $new_list[] = trim($word);
			}

			$new_list = implode(PHP_EOL,$new_list);
			if (!is_dir($folder)) {
				mkdir($folder,0777,true);
			}
			
			
			file_put_contents($file, $new_list, LOCK_EX);
			
			
		}

		$words = array();
		$chars = array();
		
		$word_count = 0;
		if (file_exists($file)){
			$lines = file($file, FILE_IGNORE_NEW_LINES);
			$words = array();
			sort($lines);
			
			foreach ($lines as $line){
				if ($line){
					$word_count = $word_count + 1;
					$firstletter = substr($line, 0, 1);
					if (!preg_match("/^[a-z]$/i", $firstletter)) {
						$firstletter = "Other";
					}
					$firstletter = ucfirst($firstletter);
					$words[$firstletter][] = $line;
				}
				
				
			}
			$ret = array();
		
			foreach ($words as $key=>$val){
				$ret[] = array(
					"char"=>$key,
					"words"=>$val
				);
				$chars[] = $key;
			}
			$words = $ret;
			
		//	test_array($words); 
			
		}
		


//test_array($pages);

		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","apps/nf/ui/");
		$tmpl->page = array(
			"section"=> "admin",
			"sub_section"=> "dictionary",
			"template"=> "admin_dictionary",
			"meta"    => array(
				"title"=> "NF - Admin - Dictionary",
			)
		);

		$tmpl->chars = $chars;
		$tmpl->word_count = $word_count;
		$tmpl->words = $words;

		$tmpl->output();

	}

}
