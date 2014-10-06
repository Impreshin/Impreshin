<?php
namespace apps\cm\controllers\data;
use \timer as timer;
use \apps\cm\models as models;
use \models\user as user;

class data_contact extends data {

	private $parent;

	function __construct($parent){
		$this->parent = $parent;
	}
	
	function data($ID){
		$return = array();
		
		$data = new models\companies();
		$data = $data->get($ID);
		
		$data = models\companies::display($data);
		
		$data['notes'] = models\companies_notes::getAll("parentID = '{$data['ID']}'","datein DESC");
		$data['tasks'] = array();
		$data['tasks_open'] = 0;
		$data['interactions'] = array();

		//$data['linked'] = models\companies::getAll("","","0,2");

		$return = $data;


		return $GLOBALS["output"]['data'] = $return;
	}
	
	function data_note($ID){
		$return = array();

		$return['details']="woof";


		return $GLOBALS["output"]['data'] = $return;
	}
	


}
