<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\nf\controllers\data;

use \timer as timer;
use \apps\nf\models as models;
use \models\user as user;


class provisional extends data {
	function __construct() {
		parent::__construct();


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
	//	$stageID = (isset($_REQUEST['stageID']) && $_REQUEST['stageID']!="") ? $_REQUEST['stageID'] : $settings['stageID'];


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
			"search"=>$search,
			//"stageID"=>$stageID

		);


		models\settings::save($values);
		
		



		$DefaultSettings = \apps\nf\settings::_available();
		if (isset($DefaultSettings['columns'][$ordering['c']])) {
			$ordering['c'] = isset($DefaultSettings['columns'][$ordering['c']]['o']) ? $DefaultSettings['columns'][$ordering['c']]['o'] : $ordering['c'];
		} else {
			$ordering['c'] = "datein";
		}


		$where = "nf_articles.cID ='".$user['company']['ID']."' ";
		
		
		
		

		//$where = "1";
		$where .= " AND archived != '1'";
		$options = array();
		if ($highlight=="used"){
			$options = array("newsbook_used"=>true);
		}
		
		
		
		
		$records = models\articles::getAll($where, $grouping, $ordering,$options);
		$stats = models\record_stats::stats($records,array("locked","stages","in_newsbook","placed"));

		$stats['percent_highlight'] = ($highlight)?$stats['records'][$highlight]['p']:"0";
		
		
		$return = array();
		$return['stats'] = $stats;
		
		$return['group'] = $grouping;
		$return['order'] = array(
			"c"=> $ordering_c,
			"o"=> $ordering_d
		);
		$return['list'] = models\articles::display($records, array("highlight"=>$highlight,"filter" => $filter));


		
		//test_array($this->f3->get("json"));
		return $GLOBALS["output"]['data'] = $return;
	}

}
