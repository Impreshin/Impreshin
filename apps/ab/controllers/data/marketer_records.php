<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\ab\controllers\data;

use \timer as timer;
use \apps\ab\models as models;
use \models\user as user;


class marketer_records extends data {
	function __construct() {
		parent::__construct();

	}
	function _list($nolimits=false) {
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];

		$currentDate = $user['publication']['current_date'];
		$dID = $currentDate['ID'];

		$section = 'marketer_records';
		$settings = models\settings::_read($section);

		$grouping_g = (isset($_REQUEST['group']) && $_REQUEST['group'] != "") ? $_REQUEST['group'] : $settings['group']['g'];
		$grouping_d = (isset($_REQUEST['groupOrder']) && $_REQUEST['groupOrder'] != "") ? $_REQUEST['groupOrder'] : $settings['group']['o'];

		$ordering_c = (isset($_REQUEST['order']) && $_REQUEST['order'] != "") ? $_REQUEST['order'] : $settings['order']['c'];
		$ordering_d = $settings['order']['o'];


		$marketerID = (isset($_REQUEST['marketerID'])) ? $_REQUEST['marketerID'] : $settings['marketerID'];
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
			"marketerID"      => $marketerID,
			"search"=>array(
				"dates"=> $search_dates
			)


		);


		models\settings::save($values);

		$DefaultSettings = \apps\ab\settings::_available();
		if (isset($DefaultSettings['columns'][$ordering['c']])) {
			$ordering['c'] = isset($DefaultSettings['columns'][$ordering['c']]['o']) ? $DefaultSettings['columns'][$ordering['c']]['o'] : $ordering['c'];
		} else {
			$ordering['c'] = "client";
		}

		//print_r($values);
		//exit();
		$orderby = " client ASC";
		$arrange = "";

		$searchsql = "AND marketerID = '{$marketerID}'";
		
		if ($search_dates){
			$search_dates = explode("to",$search_dates);

			if (count($search_dates)==1){
				$searchsql .= " AND global_dates.publish_date = '".trim($search_dates[0])."'";
			} else {
				$searchsql .= " AND (global_dates.publish_date >= '" . trim($search_dates[0])."' AND global_dates.publish_date <= '" . trim($search_dates[1])."')";
			}

		}


		$where = "(ab_bookings.pID = '$pID') AND deleted is null $searchsql";
		if (($user['permissions']['view']['only_my_records'] == '1')) {
			$where = $where . " AND userID = '" . $user['ID'] . "'";
		}


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

		//test_array($ordering); 

		$records = models\bookings::getAll($where, $grouping, $ordering, $limits);
		$stats = models\record_stats::stats($records,array("cm","checked","material","layout","totalCost"));
		$loading = models\loading::getLoading($pID,$stats['cm'], $currentDate['pages']);
		//$loading = loading::getLoading($pID,16000, $currentDate['pages']);


		$stats['loading'] = $loading;

		$return = array();



		$return['group'] = $grouping;
		$return['order'] = array("c" => $ordering_c, "o" => $ordering_d);
		$return['pagination'] = $pagination;

		$return['stats'] = $stats;




		$return['list'] = models\bookings::display($records);

		return $GLOBALS["output"]['data'] = $return;
	}

}
