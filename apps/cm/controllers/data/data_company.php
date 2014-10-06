<?php
namespace apps\cm\controllers\data;
use \timer as timer;
use \apps\cm\models as models;
use \models\user as user;

class data_company extends data {

	private $parent;

	function __construct($parent){
		$this->parent = $parent;
	}

	function data($ID){
		$return = array();
		
		$data = new models\companies();
		$data = $data->get($ID);
		
		$data = models\companies::display($data);
		
		$notes = models\companies_notes::getAll("parentID = '{$data['ID']}'","datein DESC");
		$n = array();
		foreach ($notes as $item){
			$item['timeago'] = timesince($item['datein']);
			$item['note'] = nl2br ($item['note']);
			$n[] = $item;
		}
		$notes = $n;
		
		
		$data['notes'] = $notes;
		$data['tasks'] = array();
		$data['tasks_open'] = 0;
		$data['interactions'] = array();

		//$data['linked'] = models\companies::getAll("","","0,2");

		$return = $data;


		return $return;
	}
	
	function data_note($ID){
		$return = array();
		$data = new models\companies_notes();
		$data = $data->get($ID);

		$return = $data;
		return $return;
	}
	


}
