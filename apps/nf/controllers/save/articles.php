<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\nf\controllers\save;


use \timer as timer;
use \apps\nf\models as models;
use \models\user as user;


class articles extends save {
	function __construct() {
		parent::__construct();


	}

	function form() {
		$user = $this->f3->get("user");
		$userID = $user['ID'];

		

		$ID = (isset($_GET['ID']) && $_GET['ID'] && $_GET['ID'] != "undefined") ? $_GET['ID'] : "";
		$type = (isset($_GET['type'])) ? $_GET['type'] : "";


		$values = array();

		if ($type){
			$values['typeID'] = $type;
		}

		
		
	

		
		if (isset($_POST['title'])) $values['title'] = $_POST['title'];
		if (isset($_POST['body'])) $values['body'] = $_POST['body'];
		if (isset($_POST['authorID'])) $values['authorID'] = $_POST['authorID'];
		if (isset($_POST['categoryID'])) $values['categoryID'] = $_POST['categoryID'];
		if (isset($_POST['meta'])) $values['meta'] = $_POST['meta'];
		if (isset($_POST['priority'])) $values['priority'] = $_POST['priority'];
		if (isset($_POST['cm'])) $values['cm'] = $_POST['cm'];
		if (isset($_POST['checklist'])) $values['checklist'] = $_POST['checklist'];
		if (isset($_POST['stageID'])) $values['stageID'] = $_POST['stageID'];



		$values['deleted'] = NULL;
		$values['deleted_userID'] = NULL;
		$values['deleted_user'] = NULL;
		$values['deleted_date'] = NULL;
		$values['deleted_reason'] = NULL;
		
		$ss = array();
		$ss["form"] = array(
			"type" => $type, 
			"last_author" => isset($values['authorID']) && $values['authorID'] ? $values['authorID'] : "", 
			"last_category" => isset($values['categoryID']) && $values['categoryID'] ? $values['categoryID'] : ""
		);
		
		models\settings::save($ss);

		
		
		

		$settings = \apps\nf\settings::_available();

		
		
		
		// ------------------------
		
		$ID = models\articles::save($ID,$values);
		
		
		
		
		$filetypes = $settings['allowedFileTypes'];

		$files = array();
		
		
		foreach ($_POST as $k=>$v){
					if (strpos($k,"file-filename-")===0){
						$file_ID= str_replace("file-filename-", "", $k);
						$n = $file_ID;


						$filename = isset($_POST['file-filename-' . $n]) ? $_POST['file-filename-' . $n] : "";





							$icon = "file";

							$file_ext = strtolower($filename);
							$file_ext = explode(".", $file_ext);
							$file_ext = $file_ext[count($file_ext) - 1];

							$filetype = "3";
							foreach ($filetypes as $file_k => $file_v) {
								foreach ($file_v as $file_key => $file_item) {
									if (in_array($file_ext, $file_item)) {
										$icon = $file_key;
										$filetype = $file_k;
									}
								}
							}




						$fileID = isset($_POST['file-ID-' . $n]) ? $_POST['file-ID-' . $n] : "";

						$file = array(
							"aID"=> $ID,
							"filename"=> $filename,
							"filename_orig"=> isset($_POST['file-filename_orig-' . $n]) ? $_POST['file-filename_orig-' . $n] : "",
							"caption"=> isset($_POST['file-caption-' . $n]) ? $_POST['file-caption-' . $n] : "",
							"uID"=> isset($_POST['file-uID-' . $n]) ? $_POST['file-uID-' . $n] : $user['ID'],
							"type"=> $filetype,

						);
						
						models\files::save($fileID,$file);
						
						//$files[] = $file;

					}
				}


		foreach ($files as $file){
			
		}
	
		

		

		$r = array();
		$r['ID'] = $ID;
		$r['values'] = $values;
		//$r['post'] = $_POST;
		test_array($r);
		exit();


	}



}
