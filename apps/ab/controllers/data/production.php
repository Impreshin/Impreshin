<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\ab\controllers\data;

use \timer as timer;
use \apps\ab\models as models;
use \models\user as user;


class production extends data {
	function __construct() {
		parent::__construct();

	}
	function _list() {
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];

		$currentDate = $user['publication']['current_date'];
		$dID = $currentDate['ID'];

		$section = 'production';

		$settings = models\settings::_read($section);





		$grouping_g = (isset($_REQUEST['group'])&& $_REQUEST['group']!="") ? $_REQUEST['group'] : $settings['group']['g'];
		$grouping_d = (isset($_REQUEST['groupOrder']) && $_REQUEST['groupOrder'] != "") ? $_REQUEST['groupOrder'] : $settings['group']['o'];

		$ordering_c = (isset($_REQUEST['order']) && $_REQUEST['order'] != "") ? $_REQUEST['order'] : $settings['order']['c'];
		$ordering_d = $settings['order']['o'];


		$highlight = (isset($_REQUEST['highlight']) && $_REQUEST['highlight'] != "") ? $_REQUEST['highlight'] : $settings['highlight'];
		$filter = (isset($_REQUEST['filter']) && $_REQUEST['filter']!="") ? $_REQUEST['filter'] : $settings['filter'];


		$search = (isset($_REQUEST['search']) && $_REQUEST['search'] != "") ? $_REQUEST['search'] : "";


		
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
			"group"      => $grouping,
			"order"      => $ordering,

			"highlight"=> $highlight,
			"filter"   => $filter,
			"search"=>$search

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

		$where = "(ab_bookings.pID = '$pID' AND ab_bookings.dID='$dID') AND checked = '1' AND ab_bookings.deleted is null";


		$records = models\bookings::getAll($where, $grouping, $ordering);


		$stats = models\record_stats::stats($records,array("cm","checked","material","material_approved","planned"));

		//test_array($stats); 

		if ($search) {
			$searchsql = " AND (client like '%$search%' OR ab_placing.placing like '%$search%' OR ab_marketers.marketer like '%$search%' OR ab_accounts.account like '%$search%' OR ab_accounts.accNum like '%$search%') ";
			$where .= $searchsql;
			$records = models\bookings::getAll($where, $grouping, $ordering);
		}


		$return = array();


		$return['date'] = date("d M Y",strtotime($currentDate['publish_date_display']));
		$stats['percent_highlight'] = ($highlight) ? $stats['records'][$highlight]['p'] : "0";
		$return['stats'] = $stats;
		$return['group'] = $grouping;
		$return['order'] = array("c" => $ordering_c, "o" => $ordering_d);


		if ($highlight == 'material_approved'){
			$r = array();
			foreach ($records as $record){
				if ($record['material']) $r[] = $record;
			}
			$records = $r;
		}

		$return['list'] = models\bookings::display($records, array("highlight"=> $highlight, "filter"   => $filter));

		return $GLOBALS["output"]['data'] = $return;
	}

}
