<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\ab\controllers\save;


use \timer as timer;
use \apps\ab\models as models;
use \models\user as user;


class checkboxes extends save {
	function __construct() {
		parent::__construct();
		$this->ids = (isset($_POST['ids']) && $_POST['ids'] && $_POST['ids'] != "undefined") ? $_POST['ids'] : array();
		$this->ids = explode(",",$this->ids);
	}
	
	function checked(){
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		
		
		$section = "checked";

		$values = array();
		
		$values['checked'] = '1';
		$values['checked_date'] = date("Y-m-d H:i:s");
		$values['checked_userID'] = $userID;
		





		if ($section) {
			foreach ($this->ids as $id){
				models\bookings::save($id, $values,array("section"=> $section,"dry"=>false));
			}
		}


		
			



		test_array($this->ids);
		
	}
	
	
	
	
	

	
}
