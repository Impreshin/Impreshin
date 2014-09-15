<?php
namespace apps\cm\controllers\data;
use \timer as timer;
use \apps\cm\models as models;
use \models\user as user;

class data {

	protected $f3;
	function __construct() {
		$this->f3 = \base::instance();

		$this->f3->set("json",true);
		$GLOBALS["output"]['notifications'] = \apps\cm\models\notifications::show();
	}

	function __destruct() {


	}

	function details() {
		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";
		$user = $this->f3->get("user");
		$return = array();
		
		$sec = substr($ID,0,3);
		$ID = str_replace(array("pe-","co-"),"",$ID);
		//test_array($sec); 
		
		switch($sec){
			case "co-":
				$type = "company";
				$data = $this->details_co($ID);
				break;
			case "pe-":
				$type = "contact";
				$data = $this->details_pe($ID);
				break;
			default:
				$type = "";
				$data = array();
			
		}
		$return = $data;
		$return['type']=$type;
		
		


		
		return $GLOBALS["output"]['data'] = $return;
		
	}
	function details_co($ID){
		$return = array();
		
		$data = new models\companies();
		$data = $data->get($ID);
		
		$data = models\companies::display($data);

		$return = $data;


		return $GLOBALS["output"]['data'] = $return;
	}
	function details_pe($ID){
		$return = array();
		
		$data = new models\contacts();
		$data = $data->get($ID);
		$data = models\contacts::display($data);
		$return = $data;


		return $GLOBALS["output"]['data'] = $return;
	}



}
