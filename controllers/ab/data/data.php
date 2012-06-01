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


		$record = new models\bookings();
		$return = $record->get($ID);



		$GLOBALS["output"]['data'] = $return;
	}






}
