<?php

namespace apps\cm\controllers\save;

use apps\cm\models as models;
use timer as timer;


class form extends save {
	function __construct() {
		parent::__construct();
		$this->user = $this->f3->get("user");

	}

	function form($directreturn = false) {
		$timer = new timer();
		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";

		
	
		$type = (isset($_REQUEST['type'])) ? $_REQUEST['type'] : "";
		if ($ID) {
			$type = substr($ID, 0, 2);
			$ID = preg_replace("/[^0-9]/", "", $ID);
		}

		
		



		

		switch ($type) {
			case "co":
				$data = $this->form_co($ID);
				break;
			case "pe":
				$data = $this->form_pe($ID);
				break;
			case "in":
				$data = $this->form_in($ID);
				break;
			case "ta":
				$data = $this->form_ta($ID);
				break;
			default:
				$data = array();

		}
		$data['linkID'] = $type . "-".$data['ID'];


		$return = array();
		$return['details'] = $data;
		$return['type'] = $type;




		
		






		$timer->stop("Controller - _details", array("ID" => "", "title" => ""));
		if ($directreturn) {
			return $return;
		}

		return $GLOBALS["output"]['data'] = $return;
	}

	function form_co($ID = "") {
		$return = array();

		$values = array();
		
		if (isset($_POST['company'])) $values['company'] = $_POST['company'];
		if (isset($_POST['short'])) $values['short'] = $_POST['short'];
		if (isset($_POST['taxID'])) $values['taxID'] = $_POST['taxID'];


		$values['cID'] = $this->user['company']['ID'];
		
		
		
		$ids = array();
		foreach ($_POST as $k=>$v){
			if (strpos($k,"tact-details-cat-")){
				$ids[] = str_replace("contact-details-cat-","",$k);
			}
		}
		
		$contacts = array();
		$i = 0;
		foreach ($ids as $item){
			$dID = substr($item,0,1)=="n"?"":$item;
			$item = array(
				"ID"=>$dID,
			    "catID"=>isset($_POST['contact-details-cat-'.$item])?$_POST['contact-details-cat-'.$item]:"",
			    "value"=>isset($_POST['contact-details-val-'.$item])?$_POST['contact-details-val-'.$item]:"",
			    "group"=>isset($_POST['contact-details-gro-'.$item])?$_POST['contact-details-gro-'.$item]:"",
			    "orderby"=>$i++,
			);
			
			$contacts[] = $item;
		}

		$values['details'] = $contacts;


		$l_co = array();
		foreach ($_POST as $k=>$v){
			if (strpos($k,"ink-company-field")){
				if (!in_array($v,$l_co)) $l_co[] = $v;
			}
		}
		$l_pe = array();
		foreach ($_POST as $k=>$v){
			if (strpos($k,"ink-contact-field")){
				if (!in_array($v,$l_pe)) $l_pe[] = $v;
			}
		}

		$values['linked'] = array(
			"company"=>$l_co,
			"contact"=>$l_pe,
		);
		
		//test_array($_POST);
		//test_array($values); 

		$ID = models\companies::save($ID,$values);
		


		return $ID;
	}

	function form_pe($ID = "") {
		$return = array();

		$values = array();

		if (isset($_POST['firstName'])) $values['firstName'] = $_POST['firstName'];
		if (isset($_POST['lastName'])) $values['lastName'] = $_POST['lastName'];
		if (isset($_POST['title'])) $values['title'] = $_POST['title'];


		$values['cID'] = $this->user['company']['ID'];



		$ids = array();
		foreach ($_POST as $k=>$v){
			if (strpos($k,"tact-details-cat-")){
				$ids[] = str_replace("contact-details-cat-","",$k);
			}
		}

		$contacts = array();
		$i = 0;
		foreach ($ids as $item){
			$dID = substr($item,0,1)=="n"?"":$item;
			$item = array(
				"ID"=>$dID,
				"catID"=>isset($_POST['contact-details-cat-'.$item])?$_POST['contact-details-cat-'.$item]:"",
				"value"=>isset($_POST['contact-details-val-'.$item])?$_POST['contact-details-val-'.$item]:"",
				"group"=>isset($_POST['contact-details-gro-'.$item])?$_POST['contact-details-gro-'.$item]:"",
				"orderby"=>$i++,
			);

			$contacts[] = $item;
		}

		$values['details'] = $contacts;

		//test_array($values); 

		$ID = models\contacts::save($ID,$values);



		return $ID;
	}


	function form_in($ID = "") {
		$return = array();

		$d = new models\contacts();
		$return = $d->get($ID);



		return $return;
	}

	function form_ta($ID = "") {
		$return = array();

		$d = new models\contacts();
		$return = $d->get($ID);



		return $return;
	}

}
