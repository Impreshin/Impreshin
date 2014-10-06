<?php
namespace apps\cm\controllers\save;
use \timer as timer;
use \apps\cm\models as models;
use \models\user as user;

class save_company extends save {

	private $parent;

	function __construct($parent){
		$this->parent = $parent;
		$this->user = $this->parent->user;
	}

	
	
	function note(){
		$return = array();

		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";
		$parentID = (isset($_REQUEST['parentID'])) ? $_REQUEST['parentID'] : "";
		$parentID = preg_replace("/[^0-9 ]/", '', $parentID);

		$note = (isset($_REQUEST['note'])) ? $_REQUEST['note'] : "";;
		$heading = (isset($_REQUEST['heading'])) ? $_REQUEST['heading'] : "";;
		$values = array(
			"parentID"=>$parentID,
			"uID"=>$this->user['ID'],
			"heading"=>$heading,
			"note"=>$note

		);
		//test_array($values); 
		$ID = models\companies_notes::save($ID,$values);


		return $ID;
		
	}
	function delete_note(){
		$return = array();

		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";
		
		//test_array($values); 
		$ID = models\companies_notes::_delete($ID);


		return "done";
		
	}
	


}
