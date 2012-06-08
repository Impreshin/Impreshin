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
		$pID = $user['ab_publication']['ID'];
		$dID = $user['ab_publication']['current_date']['ID'];

		if ($sectionID == "0"){
			$sectionID = "";
		}

		$values = array(
			"pID"=>$pID,
			"dID"=>$dID,
			"sectionID"=>$sectionID,
			"page"=>$page,
			"colour"=>$colour
		);
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

		$pID = $user['ab_pID'];
		$dID = $user['ab_publication']['current_date']['ID'];

		$a = new Axon("global_pages");
		$a->load("pID = '$pID' AND dID = '$dID' AND page = '$page'");
		if ($a->dry()){
			$a->pID = $pID;
			$a->dID = $dID;
			$a->page = $page;

			$a->save();;

			$page = $a->_id;
		} else {
			$page = $a->ID;
		}


		$values=array(
			"pageID"=>$page
		);
		models\bookings::save($ID, $values,array("section"=>"layout","dry"=>false));

		$data = new \controllers\ab\data\layout();
		$data = $data->_page($page);

		return $GLOBALS["output"]['data'] = $data;

	}


}
