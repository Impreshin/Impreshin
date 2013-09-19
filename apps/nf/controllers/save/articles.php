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

	function comment() {
		$user = $this->f3->get("user");
		$ID = isset($_POST['comment-ID']) ? $_POST['comment-ID'] : "";
		$aID = (isset($_GET['aID']) && $_GET['aID'] && $_GET['aID'] != "undefined") ? $_GET['aID'] : "";
		
		
		$values = array(
			"aID"=> $aID,
			"uID"=>$user['ID'],
			"comment"=>isset($_POST['comment'])? $_POST['comment']:"",
			"parentID"=>isset($_POST['comment-parentID'])&& $_POST['comment-parentID'] && $_POST['comment-parentID'] != "null" && $_POST['comment-parentID']!= "undefined"? $_POST['comment-parentID']:"",
		);
		
		models\comments::save($ID,$values);
		test_array(array("ID"=>$ID,"values"=>$values)); 
		
	}
	function newsbook() {
		$user = $this->f3->get("user");
		$ID = isset($_POST['newsbook-ID']) ? $_POST['newsbook-ID'] : "";
		$aID = (isset($_GET['aID']) && $_GET['aID'] && $_GET['aID'] != "undefined") ? $_GET['aID'] : "";
		
		
		$values = array(
			"aID"=> $aID,
			"pID"=> isset($_POST['newsbook-pID'])&& $_POST['newsbook-pID'] && $_POST['newsbook-pID'] != "null" && $_POST['newsbook-pID']!= "undefined"? $_POST['newsbook-pID']:"",
			"dID"=> isset($_POST['newsbook-dID'])&& $_POST['newsbook-dID'] && $_POST['newsbook-dID'] != "null" && $_POST['newsbook-dID']!= "undefined"? $_POST['newsbook-dID']:"",
			"uID"=>$user['ID'],
		);
		
		// do the insert thing.. get the ID
		$ID = models\newsbooks::save($ID,$values);
		
		
		$files = array();
		
		$file_ids = array();
		$sql = array();
		
		foreach (explode(",",$_POST['files']) as $fileID){
			$sql[] = "('".$ID."', '".$fileID."')";
			$files[] = array(
				"nID"=>$ID,
				"fileID"=>$fileID
			);
			$file_ids[] = $fileID;
		}
		
		if (count($sql)){
			
			$sql = "INSERT INTO nf_article_newsbook_photos (`nID`,`fileID`) VALUES ".implode(",",$sql);

			$this->f3->get("DB")->exec("DELETE FROM nf_article_newsbook_photos WHERE nID = '$ID'");
			$this->f3->get("DB")->exec($sql);
		}
		
		
		
		
		
		
		test_array(array("ID"=>$ID,"values"=>$values));

		
	}
	function remove_newsbook() {
	$ID = (isset($_GET['ID']) && $_GET['ID'] && $_GET['ID'] != "undefined") ? $_GET['ID'] : "";
	models\newsbooks::_delete($ID);
	test_array("done");
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
		if (isset($_POST['priorityID'])) $values['priorityID'] = $_POST['priorityID'];
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
		$submit = true;
		$r = array();

		if (isset($values['title'])&&$values['title']==''){
			$submit = false;
			$r['error'][] = array(
				"field"=>"title",
				"msg"=>"Need to specify a title"
			);
		}
		
		if ((isset($_POST['current_stage_ID'])&&($_POST['current_stage_ID']!='1' || (isset($_POST['stageID']) &&$_POST['stageID']!='1')))){
			if (isset($values['meta'])&&$values['meta']==''){
				$submit = false;
				$r['error'][] = array(
					"field"=>"meta",
					"msg"=>"Need to specify meta"
				);
			}
		}
		
		
		
		
		
		if ($submit){
			
		
			$ID = models\articles::save($ID,$values);
			
			
			
			
			$filetypes = $settings['allowedFileTypes'];
	
			$files = array();
			
			
			foreach ($_POST as $k=>$v){
						if (strpos($k,"file-filename-")===0){
							$file_ID= str_replace("file-filename-", "", $k);
							$n = $file_ID;
	
	
							$filename = isset($_POST['file-filename-' . $n]) ? $_POST['file-filename-' . $n] : "";
	
	
	
	
	
								$icon = "file";
								$filetype = "0";
								$file_ext = strtolower($filename);
								$file_ext = explode(".", $file_ext);
								$file_ext = $file_ext[count($file_ext) - 1];
	
								
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
		
			
	
			
	
		
			$r['ID'] = $ID;
			$r['values'] = $values;
			//$r['post'] = $_POST;
			
		}
		test_array($r);
		exit();
	}



}
