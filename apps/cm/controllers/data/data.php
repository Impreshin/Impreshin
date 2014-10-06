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
		$return['heatmap']=array(
			strtotime("now")=>3,
			strtotime("-1 day")=>7,
			strtotime("-1 month")=>5,
			strtotime("-4 month +3 day")=>1,
			strtotime("-3 month")=>12,
			strtotime("-3 month -4 day")=>3,
			//strtotime("1 September 2014")=>"2"
		);
		
		


		
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


}
