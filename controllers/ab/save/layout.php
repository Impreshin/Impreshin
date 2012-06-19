<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace controllers\ab\save;
use \F3 as F3;
use \Axon as Axon;
use \timer as timer;
use \models\ab as models;
use \models\user as user;


class layout extends save {

	function _page() {
		$user = F3::get("user");
		$page = isset($_REQUEST['page'])?$_REQUEST['page']:"";
		$sectionID = isset($_REQUEST['sectionID'])?$_REQUEST['sectionID']:"";
		$colour = isset($_REQUEST['colour'])?$_REQUEST['colour']:"";
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
		if ($colour!=""){
			if ($colour == "0") {
				$colour = "";
			}
			$values['colour']= $colour;
		}
		if ($locked!=""){
			$values['locked']= $locked;
		}



		$a = new Axon("global_pages");
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
		$user = F3::get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";
		$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : "";

		$pID = $user['pID'];
		$dID = $user['publication']['current_date']['ID'];

		if ($page && ($page != "remove") ){
			$a = new Axon("global_pages");
			$a->load("pID = '$pID' AND dID = '$dID' AND page = '$page'");
			if ($a->dry()) {
				$a->pID = $pID;
				$a->dID = $dID;
				$a->page = $page;

				$a->save();
				;

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
		models\bookings::save($ID, $values,array("section"=>"layout","dry"=>false));
		$data = array("ID"=>$ID);
		if ($page && ($page != "remove")) {
			$data = new \controllers\ab\data\layout();
			$data = $data->_page($page);
		}

		return $GLOBALS["output"]['data'] = $data;

	}
	function _force(){
		$user = F3::get("user");
		$pages = isset($_REQUEST['pages']) ? $_REQUEST['pages'] : "";

		$a = new Axon("global_dates");
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


}
