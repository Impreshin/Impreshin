<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\nf\controllers\save;


use \timer as timer;
use \apps\nf\models as models;
use \models\user as user;


class layout extends save {
	function __construct() {
		parent::__construct();


	}
	function _page() {
		$user = $this->f3->get("user");
		$page = isset($_REQUEST['page'])?$_REQUEST['page']:"";
		$sectionID = isset($_REQUEST['sectionID'])?$_REQUEST['sectionID']:"";
		$colourID = isset($_REQUEST['colourID'])?$_REQUEST['colourID']:"";
		$pID = $user['publication']['ID'];
		$dID = $user['publication']['current_date']['ID'];

		$locked = isset($_REQUEST['locked']) ? $_REQUEST['locked'] : "";



		$values = array(
			"pID"=>$pID,
			"dID"=>$dID,
			"page"=>$page,
		);

		if ($sectionID!=""){
			if ($sectionID == "0") {
				$sectionID = "";
			}
			$values['sectionID']= $sectionID;
		}

		if ($colourID != "") {
			$values['colourID'] = $colourID;
		}


		if ($locked!=""){
			$values['nf_locked']= $locked;
		}



		$a = new \DB\SQL\Mapper($this->f3->get("DB"),"global_pages");
		$a->load("page='$page' AND pID = '$pID' AND dID='$dID'");
		$changes = array();
		$material = false;
		foreach ($values as $key=> $value) {
			$cur = $a->$key;
			if ($cur != $value) {

				$changes[] = array(
					"k"=> $key,
					"v"=> $value,
					"w"=> str_replace("0000-00-00 00:00:00", "", $cur)
				);
			}
			$a->$key = $value;
		}

		$a->save();


	}
	function _drop(){
		$user = $this->f3->get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";
		$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : "";

		$pID = $user['pID'];
		$dID = $user['publication']['current_date']['ID'];

		if ($page && ($page != "remove") ){
			$a = new \DB\SQL\Mapper($this->f3->get("DB"),"global_pages");
			$a->load("pID = '$pID' AND dID = '$dID' AND page = '$page'");
			if ($a->dry()) {
				$a->pID = $pID;
				$a->dID = $dID;
				$a->page = $page;

				$a->save();

				$pageID = $a->_id;
			} else {
				$pageID = $a->ID;
			}
		} else {
			$pageID = NULL;
		}



		$values=array(
			"pageID"=> $pageID
		);

		if ($pageID){
			$sql = "UPDATE nf_article_newsbook SET pageID = '{$pageID}' WHERE aID='$ID' AND pID='$pID' AND dID='$dID'";
		} else {
			$sql = "UPDATE nf_article_newsbook SET pageID = NULL WHERE aID='$ID' AND pID='$pID' AND dID='$dID'";
		}
		
		
		
		//test_array($sql); 
		
		$this->f3->get("DB")->exec($sql);

		

		$data = array("ID" => $ID);
		if ($page && ($page != "remove")) {
			$data = new \apps\nf\controllers\data\layout();
			$data = $data->_page($page);
		}



		return $GLOBALS["output"]['data'] = $data;

	}
	function _force(){
		$user = $this->f3->get("user");
		$pages = isset($_REQUEST['pages']) ? $_REQUEST['pages'] : "";

		$a = new \DB\SQL\Mapper($this->f3->get("DB"),"global_dates");
		$a->load("ID='".$user['publication']['current_date']['ID']."'");

		if (!$a->dry()){
			if ($pages=="auto" || $pages ==""){
				$a->pages = null;
			} else {
				$a->pages = $pages;
			}
			$a->save();
		}
		return $pages;
	}

	function _upload_page(){
		$user = $this->f3->get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";
		$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : "";
		$pID = isset($_REQUEST['pID']) ? $_REQUEST['pID'] : "";
		$dID = isset($_REQUEST['dID']) ? $_REQUEST['dID'] : "";
		$filename = isset($_REQUEST['filename']) ? $_REQUEST['filename'] : "";

		$pID = $user['pID'];


		if ($page && ($page != "remove") ){
			$a = new \DB\SQL\Mapper($this->f3->get("DB"),"global_pages");
			$a->load("pID = '$pID' AND dID = '$dID' AND page = '$page'");
			if ($a->dry()) {
				$a->pID = $pID;
				$a->dID = $dID;
				$a->page = $page;
				$a->pdf = $filename;
				$a->pdf_uID = $user['ID'];
				$a->pdf_datein = date("Y-m-d H:i:s");
				$a->save();

				$pageID = $a->_id;
			} else {
				$a->pdf = $filename;
				$a->pdf_uID = $user['ID'];
				$a->pdf_datein = date("Y-m-d H:i:s");
				$a->save();
				$pageID = $a->ID;
			}
		} else {
			$pageID = NULL;
		}



		return $GLOBALS["output"]['data'] = $pageID;

	}
}
