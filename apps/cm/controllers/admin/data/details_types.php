<?php

namespace apps\cm\controllers\admin\data;

use \timer as timer;
use \apps\cm\models as models;
use \models\user as user;

class details_types extends \apps\cm\controllers\data\data {
	function __construct() {
		parent::__construct();

	}

	function _list() {

		$user = $this->f3->get("user");
		$userID = $user['ID'];

		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];





		$where = "cID='{$user['company']['ID']}'";



		$records = models\details_types::getAll($where, "orderby ASC");

		$return = array();

		$return['records'] = $records;

		return $GLOBALS["output"]['data'] = $return;
	}

	function _details() {

		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];

		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";

		$o = new models\details_types();
		$details = $o->get($ID);

		$ID = $details['ID'];


		$return = array();


		$return = array();
		


		
		$return['details'] = $details;
		


		return $GLOBALS["output"]['data'] = $return;
	}

}
