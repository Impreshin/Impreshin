<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\nf\controllers\data;

use \timer as timer;
use \apps\nf\models as models;
use \models\user as user;


class records_newsbook extends data {
	function __construct() {
		parent::__construct();

	}
	function _list($nolimits=false) {
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];
		$cID = $user['company']['ID'];

		$currentDate = $user['publication']['current_date'];
		$dID = $currentDate['ID'];

		$section = 'records_newsbook';
		$settings = models\settings::_read($section);

		$grouping_g = (isset($_REQUEST['group']) && $_REQUEST['group'] != "") ? $_REQUEST['group'] : $settings['group']['g'];
		$grouping_d = (isset($_REQUEST['groupOrder']) && $_REQUEST['groupOrder'] != "") ? $_REQUEST['groupOrder'] : $settings['group']['o'];

		$ordering_c = (isset($_REQUEST['order']) && $_REQUEST['order'] != "") ? $_REQUEST['order'] : $settings['order']['c'];
		$ordering_d = $settings['order']['o'];

		$settings_dID = isset($settings['dID']["p_".$pID]) && $settings['dID']["p_".$pID] ? $settings['dID']["p_".$pID] : $dID;

		$selected_dID = (isset($_REQUEST['dID'])) ? $_REQUEST['dID'] :$settings_dID ;
	


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
			"dID"=>array(
				"p_".$pID=>$selected_dID
			)


		);

	//	test_array($values);

		$options = array();
		models\settings::save($values);

		$DefaultSettings = \apps\nf\settings::_available();
		if (isset($DefaultSettings['columns'][$ordering['c']])) {
			$ordering['c'] = isset($DefaultSettings['columns'][$ordering['c']]['o']) ? $DefaultSettings['columns'][$ordering['c']]['o'] : $ordering['c'];
		} else {
			$ordering['c'] = "title";
		}

		//print_r($values);
		//exit();

		
	

		$searchsql = " AND global_dates.ID = '$selected_dID'";
		

		$where = "(nf_articles.cID = '$cID') AND deleted is null $searchsql";
		if (($user['permissions']['view']['only_my_records'] == '1')) {
			$where = $where . " AND authorID = '" . $user['ID'] . "'";
		}


		

		$records = models\articles::getAll($where, $grouping, $ordering, array("pID"=>$pID,"dID"=>$selected_dID));
		

		


		$return = array();



		$return['group'] = $grouping;
		$return['order'] = array("c" => $ordering_c, "o" => $ordering_d);

		$return['stats']['records']= count($records);



		$return['list'] = models\articles::display($records);

		return $GLOBALS["output"]['data'] = $return;
	}

}
