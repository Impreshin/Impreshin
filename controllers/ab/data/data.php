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


		$return['a'] = $allow;

		$GLOBALS["output"]['data'] = $return;
	}






}
