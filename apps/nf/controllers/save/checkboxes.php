<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\nf\controllers\save;


use \timer as timer;
use \apps\nf\models as models;
use \models\user as user;


class checkboxes extends save {
	function __construct() {
		parent::__construct();
		$this->ids = (isset($_POST['ids']) && $_POST['ids'] && $_POST['ids'] != "undefined") ? $_POST['ids'] : array();
		$this->ids = explode(",",$this->ids);
	}
	
	function addnewsbook(){
		$user = $this->f3->get("user");
		if ($user['permissions']['articles']['checkbox_add_to_newsbook']!='1'){
			test_array(array("error"=>"No permission")); 
		}
		$pID = isset($_POST['checkbox-menu-addnewsbook-publications'])?$_POST['checkbox-menu-addnewsbook-publications']:"";
		$dID = "";
		
		//test_array($user); 
		foreach ($user['publications'] as $pub){
			if ($pub['ID']==$pID){
				$dID = $pub['currentDateID'];
				
			}
			
		}
		
		
		$values = array(
			"pID"=>$pID,
			//"aID" => $aID, 
			"dID" => $dID, 
			"uID" => $user['ID'], 
			"placed" => 0
		);
		
		if ($pID && $dID){
			$a = new \DB\SQL\Mapper($this->f3->get("DB"),"nf_article_newsbook");
			$b = new \DB\SQL\Mapper($this->f3->get("DB"),"nf_article_newsbook_photos");
			foreach ($this->ids as $id){
				
				$value = $values;
				$value["aID"] = $id;
				//test_array($value); 

				
				$a->load("aID='".$value["aID"]."' AND pID = '".$value["pID"]."' AND dID = '".$value["dID"]."'");

				$a->aID = $value["aID"];
				$a->pID = $value["pID"];
				$a->dID = $value["dID"];
				$a->uID = $value["uID"];

				if (!$a->dry()) {
					$label = "Record Edited ";
				} else {
					$a->placed=0;
					$label = "Record Added";
				}

				$a->save();


				$ID = $a->ID;
				
				$a->reset();


				$files = models\files::getAll("aID='" . $value["aID"] . "'", "ID DESC");
				$f = array();
				foreach ($files as $file) {
					$fileID = $file["ID"];
					$retu = array();
					
					$b->load("nID='".$ID."' AND fileID = '".$fileID."'");
					$retu['pre']["nID"] = $b->nID;
					$retu['pre']["fileID"] = $b->fileID;
					$b->nID = $ID;
					$b->fileID = $fileID;

					$retu['post']["nID_"] = $b->nID;
					$retu['post']["fileID_"] = $b->fileID;
					$b->save();
					$b->reset();
					//
					//$b->reset();
					//
					$retu["vals"] = array("nID"=>$ID,"fileID"=>$fileID);

					$f[] = $retu;
				}
				//test_array($f); 
				
				
				
			}
			
		}
		
		
		
		
		
		
		test_array(array("ids"=>$this->ids,"values"=>$values)); 
	}
	
	function archive() {
		$user = $this->f3->get("user");
		if ($user['permissions']['articles']['checkbox_archive']!='1'){
			test_array(array("error"=>"No permission"));
		}
	

		$values = array(
			"archived" => "1",
			"archived_date" => date("Y-m-d H:i:s")
		);
		
		
		foreach ($this->ids as $id){
			$ID = models\articles::save($id, $values, array("dry" => false,"section"=>"archive"));
		}
		

		
		test_array($this->ids);

	}

	
}
