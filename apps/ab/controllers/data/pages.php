<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\ab\controllers\data;

use \timer as timer;
use \apps\ab\models as models;
use \models\user as user;


class pages extends data {
	function __construct() {
		parent::__construct();

	}
	function _details() {
		$page_nr = (isset($_REQUEST['page'])) ? $_REQUEST['page'] : "";
		$user = $this->f3->get("user");
		$userID = $user['ID'];

		$pID = $user['publication']['ID'];

		$dID = $user['publication']['current_date']['ID'];

		$page = models\pages::getAll("page='$page_nr' AND dID = '$dID' AND pID='$pID'");

		if (count($page)){
			$page = $page[0];
		} else {
			$page = models\pages::dbStructure();
			$page['page']= $page_nr;
		}

		$page['sql'] = "page='$page' AND dID = '$dID' AND pID='$pID'";

		$GLOBALS["output"]['data'] = $page;
	}

}
