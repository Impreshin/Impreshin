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
		$pID = $user['pID'];

		$currentDate = $user['publication']['current_date'];
		$dID = $currentDate['ID'];

		$section = 'production';
		$usersettings = $user['settings'][$section];

		$grouping_g = (isset($_REQUEST['group']) && $_REQUEST['group'] != "") ? $_REQUEST['group'] : $usersettings['group']['g'];
		$grouping_d = (isset($_REQUEST['groupOrder']) && $_REQUEST['groupOrder'] != "") ? $_REQUEST['groupOrder'] : $usersettings['group']['o'];

		$ordering_c = (isset($_REQUEST['order']) && $_REQUEST['order'] != "") ? $_REQUEST['order'] : $usersettings['order']['c'];
		$ordering_d = $usersettings['order']['o'];


		$highlight = (isset($_REQUEST['highlight']) && $_REQUEST['highlight'] != "") ? $_REQUEST['highlight'] : $usersettings['highlight'];
		$filter = (isset($_REQUEST['filter']) && $_REQUEST['filter'] != "") ? $_REQUEST['filter'] : $usersettings['filter'];


		if ((isset($_REQUEST['order']) && $_REQUEST['order'] != "")) {
			if ($user['settings'][$section]['order']['c'] == $_REQUEST['order']) {
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
		$values[$section] = array(
			"group"      => $grouping,
			"order"      => $ordering,

			"highlight"=> $highlight,
			"filter"   => $filter

		);

		models\user_settings::save_setting($values);


		//print_r($values);
		//exit();
		$orderby = " client ASC";
		$arrange = "";

		$where = "(ab_bookings.pID = '$pID' AND ab_bookings.dID='$dID') AND checked = '1' AND ab_bookings.deleted is null";


		$records = models\bookings::getAll($where, $grouping, $ordering);


		$stats = models\record_stats::stats($records,array("cm","checked","material","material_approved"));



		$return = array();


		$return['date'] = $currentDate['publish_date_display'];
		$stats['percent_highlight'] = ($highlight) ? $stats['records'][$highlight]['p'] : "0";
		$return['stats'] = $stats;
		$return['group'] = $grouping;
		$return['order'] = $ordering;


		if ($highlight == 'material_approved'){
			$r = array();
			foreach ($records as $record){
				if ($record['material']) $r[] = $record;
			}
			$records = $r;
		}

		$return['list'] = models\bookings::display($records, array("highlight"=> $highlight, "filter"   => $filter));

		$GLOBALS["output"]['data'] = $return;
	}

}
