<?php
namespace apps\cm\controllers\data;
use \timer as timer;
use \apps\cm\models as models;
use \models\user as user;

class data {

	protected $f3;
	function __construct() {
		$this->f3 = \base::instance();
		$this->user =  $this->f3->get("user");

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
		$ID = preg_replace("/[^0-9 ]/", '', $ID);
		//test_array($sec); 
		
		switch($sec){
			case "co-":
				$type = "company";
				$child = new data_company($this);
				$data = $child->data($ID);
				break;
			case "pe-":
				$type = "contact";
				$child = new data_contact($this);
				$data = $child->data($ID);
				break;
			default:
				$type = "";
				$data = array();
			
		}
		$return = $data;
		$return['type']=$type;
		
		$heatmap = array();
		
		foreach ($data['notes'] as $item){
			$k = strtotime($item['datein']);
			if (!isset($heatmap[$k])){
				$heatmap[$k] = 0;
			}
			$heatmap[$k] = $heatmap[$k] + 1;
		}
		foreach ($data['interactions'] as $item){
			$k = strtotime($item['datein']);
			if (!isset($heatmap[$k])){
				$heatmap[$k] = 0;
			}
			$heatmap[$k] = $heatmap[$k] + 1;
		}
		foreach ($data['tasks'] as $item){
			$k = strtotime($item['datein']);
			if (!isset($heatmap[$k])){
				$heatmap[$k] = 0;
			}
			$heatmap[$k] = $heatmap[$k] + 1;
		}
		
		
		//test_array($heatmap); 
		
		
		
		
		
		
		
		
		
		$return['heatmap']=$heatmap;
		
		


		
		return $GLOBALS["output"]['data'] = $return;
		
	}
	

	function details_note(){
		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";
		$parentID = (isset($_REQUEST['parentID'])) ? $_REQUEST['parentID'] : "";
		$user = $this->f3->get("user");
		$return = array();

		$sec = substr($parentID,0,3);
		$parentID = preg_replace("/[^0-9 ]/", '', $parentID);
		//test_array($sec); 
		

		switch($sec){
			case "co-":
				$type = "company";
				$child = new data_company($this);
				$data = $child->data_note($ID);
				break;
			case "pe-":
				$type = "contact";
				$child = new data_contact($this);
				$data = $child->data_note($ID);
				break;
			default:
				$type = "";
				$data = array();

		}
	//	test_array($data); 

		$data['type']=$type;
		$data['parentID']=$parentID;
		$return = $data;

		return $GLOBALS["output"]['data'] = $return;
	}
	function details_interaction(){
		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";
		$parentID = (isset($_REQUEST['parentID'])) ? $_REQUEST['parentID'] : "";
		$settings = models\settings::_read("details");
		$typeID = (isset($_REQUEST['type'])) ? $_REQUEST['type'] : "";
		$user = $this->f3->get("user");
		$return = array();

		$sec = substr($parentID,0,3);
		$parentID = preg_replace("/[^0-9 ]/", '', $parentID);
		//test_array($sec); 

		

		

		if ($typeID!=""){
			if (($typeID != $settings['interactions']['typeID'])){
				$values["details"] = array(
					"interactions" => array(
						"typeID"=>$typeID
					)
				);
				models\settings::save($values);
			}
			
		} else {
			$typeID = $settings['interactions']['typeID'];
		}
		
		

		switch($sec){
			case "co-":
				$child = new data_company($this);
				$data = $child->data_interaction($ID);
				break;
			case "pe-":
				$child = new data_contact($this);
				$data = $child->data_interaction($ID);
				break;
			default:
				$data = array();

		}
		
		if ($data['typeID']==""){
			$data['typeID']=$typeID;
		}
	//	test_array($data); 

		//$data['type']=$type;
		//
		$data['parentID']=$parentID;
		$return = $data;

		return $GLOBALS["output"]['data'] = $return;
	}


}
