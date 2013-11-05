<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\nf\controllers\admin\data;
use \timer as timer;
use \apps\nf\models as models;
use \models\user as user;


class resources extends \apps\nf\controllers\data\data {
	function __construct() {
		parent::__construct();

	}

	function _list() {

		$user = $this->f3->get("user");
		$userID = $user['ID'];

		
		$cID = $user['company']['ID'];





		$where = "";



		$records = models\resources::getAll($where, "orderby ASC");

		$return = array();

		$return['records'] = $records;

		return $GLOBALS["output"]['data'] = $return;
	}

	function _details() {

		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$cID = $user['company']['ID'];

		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";

		$o = new models\resources();
		$details = $o->get($ID);

		$ID = $details['ID'];


		$return = array();


		$return = array();
		
		$return['details'] = $details;
		


		return $GLOBALS["output"]['data'] = $return;
	}

}
