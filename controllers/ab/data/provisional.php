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


class provisional extends data {
	function __construct() {
		$this->f3 = \base::instance();
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		if (!$userID) exit(json_encode(array("error" => $this->f3->get("system")->error("U01"))));

	}

	function _list() {
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];




		$currentDate = $user['publication']['current_date'];
		$dID = $currentDate['ID'];

		$section = 'provisional';
		$settings = models\settings::_read($section);

		$grouping_g = (isset($_REQUEST['group'])&& $_REQUEST['group']!="") ? $_REQUEST['group'] : $settings['group']['g'];
		$grouping_d = (isset($_REQUEST['groupOrder']) && $_REQUEST['groupOrder'] != "") ? $_REQUEST['groupOrder'] : $settings['group']['o'];

		$ordering_c = (isset($_REQUEST['order']) && $_REQUEST['order'] != "") ? $_REQUEST['order'] : $settings['order']['c'];
		$ordering_d = $settings['order']['o'];


		$highlight = (isset($_REQUEST['highlight']) && $_REQUEST['highlight'] != "") ? $_REQUEST['highlight'] : $settings['highlight'];
		$filter = (isset($_REQUEST['filter']) && $_REQUEST['filter']!="") ? $_REQUEST['filter'] : $settings['filter'];


		$search = (isset($_REQUEST['search']) && $_REQUEST['search']!="") ? $_REQUEST['search'] : "";


		if ((isset($_REQUEST['order']) && $_REQUEST['order'] != "")){
			if ($settings['order']['c'] == $_REQUEST['order']){
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
		$values[$section] = array(
			"group"=> $grouping,
			"order"=> $ordering,

			"highlight"=> $highlight,
			"filter"=>$filter,
			"search"=>$search

		);


		models\user_settings::save_setting($values);


		//print_r($values);
		//exit();
		$orderby = " client ASC";
		$arrange = "";



		$where = "(ab_bookings.pID = '$pID' AND ab_bookings.dID='$dID') AND ab_bookings.deleted is null";



		$records = models\bookings::getAll($where, $grouping, $ordering);




		$stats = models\record_stats::stats($records,array("cm","checked","material","layout","totalCost"));
		$loading = models\loading::getLoading($pID,$stats['cm'], $currentDate['pages']);
		//$loading = loading::getLoading($pID,16000, $currentDate['pages']);

		if ($search){
			$searchsql = " AND (client like '%$search%' OR ab_placing.placing like '%$search%' OR ab_marketers.marketer like '%$search%' OR ab_accounts.account like '%$search%' OR ab_accounts.accNum like '%$search%') ";
			$where .= $searchsql;
			$records = models\bookings::getAll($where, $grouping, $ordering);
		}

//		test_array($loading);
		$stats['loading'] = $loading;

		$return = array();

		$return['date'] = date("d M Y",strtotime($currentDate['publish_date_display']));
		$return['dID'] = $currentDate['ID'];
		$stats['percent_highlight'] = ($highlight)?$stats['records'][$highlight]['p']:"0";
		$return['stats'] = $stats;
		$return['group'] = $grouping;
		$return['order'] = $ordering;


		$return['list'] = models\bookings::display($records, array("highlight"=>$highlight,"filter"=>$filter));
		return $GLOBALS["output"]['data'] = $return;
	}

}
