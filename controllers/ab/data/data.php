<?php
namespace controllers\ab\data;
use \F3 as F3;
use \timer as timer;
use \models\ab as models;
use \models\user as user;

class data {

	function __construct() {

		$user = F3::get("user");
		$userID = $user['ID'];
		if (!$userID) exit(json_encode(array("error" => F3::get("system")->error("U01"))));

	}

	function __destruct() {


	}


	function details(){

		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : exit(json_encode(array("error"=> F3::get("system")->error("B01"))));

		$user = F3::get("user");

		$cfg = F3::get("cfg");
		$record = new models\bookings();
		$return = $record->get($ID);




		$allow = array(
			"repeat"=>0,
			"edit"=>0,
			"print"=>1,
			"material_pane"=>0,
			"checked"=>0,
			"material"=>0

		);

		if (!$return['deleted']){

			if (!$return['accountBlocked']){
				$allow['repeat'] = '1';
			}



			if ($return['state']=='Current'){
				$allow['checked'] = '1';
			}
			if ($return['state'] == 'Current' || $return['state'] == 'Future'){
				$allow['material'] = '1';
				$allow['edit'] = '1';
			}
			if ($cfg['upload']['material']) $allow['material_pane'] = '1';
		}

		$permissions = $user['permissions'];
		if ($permissions['details']['actions']['check']=='0') $allow['checked'] = 0;
		if ($permissions['details']['actions']['material']=='0') $allow['material'] = 0;
		if ($permissions['details']['actions']['repeat']=='0') $allow['repeat'] = 0;
		if ($permissions['details']['actions']['edit']=='0') $allow['edit'] = 0;
		//if ($permissions['actions']['delete']=='0') $allow['delete'] = '0';


		$return['a'] = $allow;

		return $GLOBALS["output"]['data'] = $return;
	}






}
