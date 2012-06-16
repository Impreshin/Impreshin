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


class form extends data {
	function account_lookup_history_suggestions(){
		$accNum = (isset($_REQUEST['accNum'])) ? $_REQUEST['accNum'] : "";
		$limit = (isset($_REQUEST['limit'])) ? "LIMIT " . $_REQUEST['limit'] : "";
		$type = (isset($_REQUEST['type'])) ? $_REQUEST['type'] : "1";
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];

		$timer = new timer();

		if ($accNum){

			$where = "AND accNum = '$accNum' AND pID = '$pID' AND DATE_SUB(now(),INTERVAL '60' DAY) < publishDate ORDER BY publishDate DESC";


			$placing = F3::get("DB")->exec("
				SELECT *,
					(SELECT count(placingID) FROM ab_bookings WHERE ab_bookings.placingID = ab_placing.ID $where) AS count
				FROM `ab_placing`
				WHERE pID='$pID'
				ORDER BY count DESC
				$limit

			");
			$marketers = F3::get("DB")->exec("
				SELECT ab_marketers.*, ab_marketers_pub.pID,
					(SELECT count(marketerID) FROM ab_bookings WHERE ab_bookings.marketerID = ab_marketers.ID $where) AS count
				FROM ab_marketers INNER JOIN ab_marketers_pub ON ab_marketers.ID = ab_marketers_pub.mID
				WHERE ab_marketers_pub.pID='$pID'
				ORDER BY count DESC
				$limit
			");
			$categories = F3::get("DB")->exec("
				SELECT ab_categories.*, ab_category_pub.pID,
					(SELECT count(categoryID) FROM ab_bookings WHERE ab_bookings.categoryID = ab_categories.ID $where) AS count
				FROM ab_categories INNER JOIN ab_category_pub ON ab_categories.ID = ab_category_pub.catID
				WHERE ab_category_pub.pID='$pID'
				ORDER BY count DESC
				$limit
			");

			$a = array();
			foreach ($placing as $record){
				if ($record['count']>0)$a[] = $record;
			}
			$placing = $a;
			$a = array();
			foreach ($marketers as $record){
				if ($record['count']>0)$a[] = $record;
			}
			$marketers = $a;
			$a = array();
			foreach ($categories as $record){
				if ($record['count']>0)$a[] = $record;
			}
			$categories = $a;



			$return = array();

			if ($type=='1')	$return["placing"]= $placing;
				$return["marketers"]= $marketers;
				$return["category"]= $categories;
		} else {
			$accounts = F3::get("DB")->exec("
				SELECT DISTINCT ab_accounts.*
				FROM ab_bookings INNER JOIN ab_accounts ON ab_bookings.accNum = ab_accounts.accNum
				WHERE ab_bookings.userID = '$userID' AND pID = '$pID'
				ORDER BY publishDate DESC
				LIMIT 0,5
			");
			$return = array(
				'accounts'=> $accounts
			);
		}

		$timer->stop("Controller - account_lookup_history_suggestions", array("accNum"=>$accNum, "pID"=>$pID));
		return $GLOBALS["output"]['data'] = $return;
	}

}
