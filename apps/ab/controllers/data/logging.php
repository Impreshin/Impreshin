<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\ab\controllers\data;

use \timer as timer;
use \apps\ab\models as models;
use \models\user as user;


class logging extends data {
	function __construct() {
		parent::__construct();

	}


	function getSection(){
		$section = $this->f3->get('PARAMS.function');
		$user = $this->f3->get("user");

		$cID = $user['company']['ID'];

		$where = "cID='$cID' AND section='$section'";
		if (!in_array($section, array(""))) {
			$where .= " AND app = 'ab'";
		}
		$return = \models\logging::getAll($where, "datein DESC");

		return $GLOBALS["output"]['data'] = $return;
	}


}
