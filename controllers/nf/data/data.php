<?php
namespace controllers\nf\data;
use \F3 as F3;
use \timer as timer;
use \models\nf as models;
use \models\user as user;

class data {

	function __construct() {
		$this->f3 = \base::instance();
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		if (!$userID) exit(json_encode(array("error" => $this->f3->get("system")->error("U01"))));

	}

	function __destruct() {


	}


	function details(){

		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : exit(json_encode(array("error"=> $this->f3->get("system")->error("B01"))));
		$edit = (isset($_REQUEST['edit'])) ? $_REQUEST['edit'] : "";

		$user = $this->f3->get("user");

		$cfg = $this->f3->get("cfg");
		$record = new models\articles();
		$return = $record->get($ID);


		$edits = models\articles::getEdits($return['ID']);



		$allow = array(
			"edit"=>0,
			"print"=>1,

		);




		$permissions = $user['permissions'];
		//if ($permissions['details']['actions']['check']=='0') $allow['checked'] = 0;


		$return['a'] = $allow;
		$return['edits'] = $edits;
		$return['comments'] = array(
			"count"    => 6,
			"unread"   => 0,
			"comments" => array()
		);


		return $GLOBALS["output"]['data'] = $return;
	}






}
