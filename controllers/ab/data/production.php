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


class production extends data {
	function _list() {
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['ab_pID'];

		$currentDate = models\dates::getCurrent($pID);
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

		$where = "(ab_bookings.pID = '$pID' AND dID='$dID') AND checked = '1' AND ab_bookings.deleted is null";


		$records = models\bookings::getAll($where, $grouping, $ordering);


		$stats = models\record_stats::stats_production($records);



		$return = array();


		$return['date'] = $currentDate['publish_date_display'];
		$stats['percent_highlight'] = ($highlight) ? $stats['records'][$highlight]['p'] : "0";
		$return['stats'] = $stats;
		$return['group'] = $grouping;
		$return['order'] = $ordering;


		$return['list'] = models\bookings::display($records, array("highlight"=> $highlight, "filter"   => $filter));

		$GLOBALS["output"]['data'] = $return;
	}

}
