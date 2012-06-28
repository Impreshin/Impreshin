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
	function __construct() {

		$user = F3::get("user");
		$userID = $user['ID'];
		if (!$userID) exit(json_encode(array("error" => F3::get("system")->error("U01"))));

	}
	function _list($nolimits=false) {
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];

		$currentDate = $user['publication']['current_date'];
		$dID = $currentDate['ID'];

		$section = 'search';
		$settings = models\settings::_read($section);

		$grouping_g = (isset($_REQUEST['group']) && $_REQUEST['group'] != "") ? $_REQUEST['group'] : $settings['group']['g'];
		$grouping_d = (isset($_REQUEST['groupOrder']) && $_REQUEST['groupOrder'] != "") ? $_REQUEST['groupOrder'] : $settings['group']['o'];

		$ordering_c = (isset($_REQUEST['order']) && $_REQUEST['order'] != "") ? $_REQUEST['order'] : $settings['order']['c'];
		$ordering_d = $settings['order']['o'];


		$search_string = (isset($_REQUEST['search'])) ? $_REQUEST['search'] : $settings['search']['search'];
		$search_dates = (isset($_REQUEST['dates']) && $_REQUEST['dates'] != "") ? $_REQUEST['dates'] : $settings['search']['dates'];


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
			"search"=>array(
				"search"=> $search_string,
				"dates"=> $search_dates
			)


		);


		models\user_settings::save_setting($values);


		//print_r($values);
		//exit();
		$orderby = " client ASC";
		$arrange = "";

		$searchsql = "";
		if ($search_string){
			$searchsql .= " AND (client like '%$search_string%' OR ab_placing.placing like '%$search_string%' OR ab_marketers.marketer like '%$search_string%' OR ab_accounts.account like '%$search_string%' OR ab_accounts.accNum like '%$search_string%') ";
		}
		if ($search_dates){
			$search_dates = explode("to",$search_dates);

			if (count($search_dates)==1){
				$searchsql .= " AND global_dates.publish_date = '".$search_dates[0]."'";
			} else {
				$searchsql .= " AND (global_dates.publish_date >= '" . $search_dates[0]."' AND global_dates.publish_date <= '" . $search_dates[1]."')";
			}

		}


		$where = "(ab_bookings.pID = '$pID') AND deleted is null $searchsql";

		$selectedpage = (isset($_REQUEST['page'])) ? $_REQUEST['page'] :"";
		if (!$selectedpage) $selectedpage = 1;

		$recordsFound = models\bookings::getAll_count($where);

		//$selectedpage = 2;
		//$recordsFound = 55;


		if ($nolimits==true) {
			$limits = false;
			$pagination = array();
		} else {
			$limit = 100;
			$pagination = new \pagination();
			$pagination = $pagination->calculate_pages($recordsFound, $limit,$selectedpage, 19);

			//test_array($pagination);

			$limits = array("limit"=> $pagination['limit']);


		}


		$records = models\bookings::getAll($where, $grouping, $ordering, $limits);




		$return = array();



		$return['group'] = $grouping;
		$return['order'] = $ordering;
		$return['pagination'] = $pagination;

		$return['stats']['records']= $recordsFound;




		$return['list'] = models\bookings::display($records);

		return $GLOBALS["output"]['data'] = $return;
	}

}
