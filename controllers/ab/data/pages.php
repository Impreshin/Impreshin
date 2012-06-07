<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace controllers\ab\data;
use \F3 as F3;
use \timer as timer;
use \models\ab as models;
use \models\user as user;


class pages extends data {
	function _details() {
		$page_nr = (isset($_REQUEST['page'])) ? $_REQUEST['page'] : "";
		$user = F3::get("user");
		$userID = $user['ID'];

		$pID = $user['ab_publication']['ID'];

		$dID = $user['ab_publication']['current_date']['ID'];

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
