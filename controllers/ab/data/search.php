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


class search extends data {
	function _list() {
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['ab_pID'];

		$currentDate = $user['ab_publication']['current_date'];
		$dID = $currentDate['ID'];

		$section = 'search';
		$usersettings = $user['settings'][$section];

		$grouping_g = (isset($_REQUEST['group']) && $_REQUEST['group'] != "") ? $_REQUEST['group'] : $usersettings['group']['g'];
		$grouping_d = (isset($_REQUEST['groupOrder']) && $_REQUEST['groupOrder'] != "") ? $_REQUEST['groupOrder'] : $usersettings['group']['o'];

		$ordering_c = (isset($_REQUEST['order']) && $_REQUEST['order'] != "") ? $_REQUEST['order'] : $usersettings['order']['c'];
		$ordering_d = $usersettings['order']['o'];





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
		$values[$section] = array(
			"group"      => $grouping,
			"order"      => $ordering,


		);

		user::save_setting($values);


		//print_r($values);
		//exit();
		$orderby = " client ASC";
		$arrange = "";

		$where = "(ab_bookings.pID = '$pID' AND ab_bookings.dID='$dID')  AND ab_bookings.deleted is null";


		$records = models\bookings::getAll($where, $grouping, $ordering);




		$return = array();



		$return['group'] = $grouping;
		$return['order'] = $ordering;




		$return['list'] = models\bookings::display($records);

		$GLOBALS["output"]['data'] = $return;
	}

}
