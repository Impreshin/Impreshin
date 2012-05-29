<?php
namespace controllers\ab;
use \F3 as F3;
use \timer as timer;
use models\ab as models;
use models\ab\bookings as bookings;
use models\ab\dates as dates;
use models\ab\loading as loading;
use models\ab\record_stats as record_stats;
use models\user as user;

class _data {

	function __construct() {

		$user = F3::get("user");
		$userID = $user['ID'];
		if (!$userID) exit(json_encode(array("error" => F3::get("system")->error("U01"))));

	}

	function __destruct() {


	}

	function bookings() {
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['ab_pID'];

		$currentDate = dates::getCurrent($pID);
		$dID = $currentDate['ID'];

		$grouping_g = (isset($_REQUEST['group'])&& $_REQUEST['group']!="") ? $_REQUEST['group'] : $user['settings']['list']['group']['g'];
		$grouping_d = (isset($_REQUEST['groupOrder']) && $_REQUEST['groupOrder'] != "") ? $_REQUEST['groupOrder'] : $user['settings']['list']['group']['o'];

		$ordering_c = (isset($_REQUEST['order']) && $_REQUEST['order'] != "") ? $_REQUEST['order'] : $user['settings']['list']['order']['c'];
		$ordering_d = $user['settings']['list']['order']['o'];


		$highlight = (isset($_REQUEST['highlight']) && $_REQUEST['highlight'] != "") ? $_REQUEST['highlight'] : $user['settings']['list']['highlight'];
		$filter = (isset($_REQUEST['filter']) && $_REQUEST['filter']!="") ? $_REQUEST['filter'] : $user['settings']['list']['filter'];


		if ((isset($_REQUEST['order']) && $_REQUEST['order'] != "")){
			if ($user['settings']['list']['order']['c'] == $_REQUEST['order']){
				if ($ordering_d=="ASC"){
					$ordering_d = "DESC";
				} else {
					$ordering_d = "ASC";
				}

			}

		}

		$grouping = array(
			"g"=> $grouping_g,
			"o"=> $grouping_d
		);
		$ordering = array(
			"c"=> $ordering_c,
			"o"=> $ordering_d
		);

		$values = array();
		$values["list"] = array(
			"group"=> $grouping,
			"order"=> $ordering,

			"highlight"=> $highlight,
			"filter"=>$filter

		);

	//	test_array($values);
		user::save_setting($values);


		//print_r($values);
		//exit();
		$orderby = " client ASC";
		$arrange = "";

		//echo $orderby;
		//exit();
		$dateSQL = "AND dID = COALESCE((SELECT ID FROM global_dates WHERE global_dates.pID = '$pID' AND ab_current='1' ORDER BY publish_date DESC), (SELECT ID FROM global_dates WHERE global_dates.pID = '$pID' ORDER BY publish_date DESC)) ";

		$where = "ab_bookings.pID = '$pID' AND dID='$dID' ";



		$records = bookings::getAll($where, $grouping, $ordering);


		$stats = record_stats::stats_list($records);
		$loading = loading::getLoading($pID,$stats['cm']);

		$stats['loading'] = $loading;

		$return = array();

		$return['date'] = $currentDate['publish_date_display'];
		$stats['percent_highlight'] = ($highlight)?$stats['records'][$highlight]['p']:"0";
		$return['stats'] = $stats;
		$return['group'] = $grouping;
		$return['order'] = $ordering;


		$return['list'] = bookings::display($records, array("highlight"=>$highlight,"filter"=>$filter));
		$GLOBALS["output"]['data'] = $return;
	}
	function details(){
		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : exit(json_encode(array("error"=> F3::get("system")->error("B01"))));


		$record = new bookings();
		$return = $record->get($ID);



		$GLOBALS["output"]['data'] = $return;
	}
	function account_lookup_history_suggestions(){
		$accNum = (isset($_REQUEST['accNum'])) ? $_REQUEST['accNum'] : "";
		$limit = (isset($_REQUEST['limit'])) ? "LIMIT " . $_REQUEST['limit'] : "";
		$type = (isset($_REQUEST['type'])) ? $_REQUEST['type'] : "1";
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['ab_pID'];

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
				 SELECT *
				 FROM (ab_accounts INNER JOIN
				(SELECT DISTINCT accNum
					FROM ab_bookings
					WHERE ab_bookings.userID = '$userID' AND pID = '$pID'
					ORDER BY ab_bookings.publishDate DESC LIMIT 0,5) AS accountNumbers ON ab_accounts.accNum = accountNumbers.accNum)
				INNER JOIN ab_accounts_status ON ab_accounts.statusID = ab_accounts_status.ID



			"
			);
			$return = array(
				'accounts'=> $accounts
			);
		}

		$timer->stop("Controller - account_lookup_history_suggestions", array("accNum"=>$accNum, "pID"=>$pID));
		$GLOBALS["output"]['data'] = $return;
	}

	function production() {
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['ab_pID'];

		$currentDate = dates::getCurrent($pID);
		$dID = $currentDate['ID'];

		$grouping_g = (isset($_REQUEST['group']) && $_REQUEST['group'] != "") ? $_REQUEST['group'] : $user['settings']['production']['group']['g'];
		$grouping_d = (isset($_REQUEST['groupOrder']) && $_REQUEST['groupOrder'] != "") ? $_REQUEST['groupOrder'] : $user['settings']['production']['group']['o'];

		$ordering_c = (isset($_REQUEST['order']) && $_REQUEST['order'] != "") ? $_REQUEST['order'] : $user['settings']['production']['order']['c'];
		$ordering_d = $user['settings']['production']['order']['o'];


		$highlight = (isset($_REQUEST['highlight']) && $_REQUEST['highlight'] != "") ? $_REQUEST['highlight'] : $user['settings']['production']['highlight'];
		$filter = (isset($_REQUEST['filter']) && $_REQUEST['filter'] != "") ? $_REQUEST['filter'] : $user['settings']['production']['filter'];


		if ((isset($_REQUEST['order']) && $_REQUEST['order'] != "")) {
			if ($user['settings']['list']['order']['c'] == $_REQUEST['order']) {
				if ($ordering_d == "ASC") {
					$ordering_d = "DESC";
				} else {
					$ordering_d = "ASC";
				}

			}

		}

		$grouping = array(
			"g"=> $grouping_g,
			"o"=> $grouping_d
		);
		$ordering = array(
			"c"=> $ordering_c,
			"o"=> $ordering_d
		);

		$values = array();
		$values["production"] = array(
			"group"      => $grouping,
			"order"      => $ordering,

			"highlight"=> $highlight,
			"filter"   => $filter

		);

		user::save_setting($values);


		//print_r($values);
		//exit();
		$orderby = " client ASC";
		$arrange = "";

		//echo $orderby;
		//exit();
		$dateSQL = "AND dID = COALESCE((SELECT ID FROM global_dates WHERE global_dates.pID = '$pID' AND ab_current='1' ORDER BY publish_date DESC), (SELECT ID FROM global_dates WHERE global_dates.pID = '$pID' ORDER BY publish_date DESC)) ";

		$where = "ab_bookings.pID = '$pID' AND dID='$dID' ";


		$records = bookings::getAll($where, $grouping, $ordering);


		$stats = record_stats::stats_production($records);



		$return = array();


		$return['date'] = $currentDate['publish_date_display'];
		$stats['percent_highlight'] = ($highlight) ? $stats['records'][$highlight]['p'] : "0";
		$return['stats'] = $stats;
		$return['group'] = $grouping;
		$return['order'] = $ordering;


		$return['list'] = bookings::display($records, array("highlight"=> $highlight, "filter"   => $filter));

		$GLOBALS["output"]['data'] = $return;
	}

}
