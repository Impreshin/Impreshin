<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace controllers\nf\data;

use \F3 as F3;
use \timer as timer;
use \models\nf as models;
use \models\user as user;


class provisional extends data {
	function __construct() {

		$user = F3::get("user");
		$userID = $user['ID'];
		if (!$userID) exit(json_encode(array("error" => F3::get("system")->error("U01"))));

	}

	function _list() {
		$user = F3::get("user");
		$uID = $user['ID'];
		$pID = $user['pID'];
		$cID = $user['company']['ID'];


		$currentDate = $user['publication']['current_date'];
		$dID = $currentDate['ID'];

		$section = 'provisional';

		$settings = models\settings::_read($section);


		$grouping_g = (isset($_REQUEST['group']) && $_REQUEST['group'] != "") ? $_REQUEST['group'] : $settings['group']['g'];
		$grouping_d = (isset($_REQUEST['groupOrder']) && $_REQUEST['groupOrder'] != "") ? $_REQUEST['groupOrder'] : $settings['group']['o'];

		$ordering_c = (isset($_REQUEST['order']) && $_REQUEST['order'] != "") ? $_REQUEST['order'] : $settings['order']['c'];
		$ordering_d = $settings['order']['o'];


		$stage = (isset($_REQUEST['stage']) && $_REQUEST['stage'] != "") ? $_REQUEST['stage'] : $settings['stage'];
		$status = (isset($_REQUEST['status']) && $_REQUEST['status'] != "") ? $_REQUEST['status'] : $settings['status'];
		$authorID = (isset($_REQUEST['authorID']) && $_REQUEST['authorID'] != "") ? $_REQUEST['authorID'] : $settings['authorID'];


		if ((isset($_REQUEST['order']) && $_REQUEST['order'] != "")) {
			if ($settings['order']['c'] == $_REQUEST['order']) {
				if ($ordering_d == "ASC") {
					$ordering_d = "DESC";
				} else {
					$ordering_d = "ASC";
				}

			}

		}

		$grouping = array(
			"g" => $grouping_g,
			"o" => $grouping_d
		);
		$ordering = array(
			"c" => $ordering_c,
			"o" => $ordering_d
		);

		$values = array();
		$values[$section] = array(
			"group"  => $grouping,
			"order"  => $ordering,
			"stage"  => $stage,
			"status" => $status,
			"authorID" => $authorID

		);


		models\user_settings::save_setting($values);


		//print_r($values);
		//exit();
		$orderby = " client ASC";
		$arrange = "";
		$return = array();
		$where = "(nf_articles.cID = '$cID') AND nf_articles.deleted is null AND (SELECT count(ID) FROM nf_article_newsbook WHERE nf_article_newsbook.aID = nf_articles.ID)='0' ";
		if ($authorID&&$authorID!='0'){
			$where .= " AND authorID = '$authorID' ";
		}






		switch ($status) {
			case '1':
				$where .= " AND (lockedBy = '$uID')";
				break;
			case '0':
				$where .= " AND (lockedBy is null)";
				break;


		}






		$records = models\articles::getAll($where, $grouping, $ordering);
		$stats = models\record_stats::stats($records, array("stages"));


		$return['group'] = $grouping;

		$return['order'] = $ordering;
		$return['stats'] = $stats;
		$return['list'] = models\articles::display($records, array("stage" => $stage, "status" => $status));

		return $GLOBALS["output"]['data'] = $return;
	}

}
