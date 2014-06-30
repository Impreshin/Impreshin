<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\ab\controllers\data;

use models\global_colours;
use \timer as timer;
use \apps\ab\models as models;
use \models\user as user;


class admin_company extends data {
	function __construct() {
		parent::__construct();

	}

	
	function _details() {
		$user = $this->f3->get("user");
		$cfg = $this->f3->get("CFG");

		$return = $user['company'];

		return $GLOBALS["output"]['data'] = $return;
	}

}
