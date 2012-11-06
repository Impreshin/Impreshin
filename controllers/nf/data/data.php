<?php
namespace controllers\nf\data;
use \F3 as F3;
use \timer as timer;
use \models\nf as models;
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
		$record = new models\articles();
		$return = $record->get($ID);





		$allow = array(
			"edit"=>0,
			"print"=>1,

		);




		$permissions = $user['permissions'];
		//if ($permissions['details']['actions']['check']=='0') $allow['checked'] = 0;


		$return['a'] = $allow;

		return $GLOBALS["output"]['data'] = $return;
	}






}
