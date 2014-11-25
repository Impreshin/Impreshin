<?php

namespace apps\cm\controllers\data;

use \timer as timer;
use \apps\cm\models as models;
use \models\user as user;


class watchlist extends data {
	function __construct() {
		parent::__construct();


	}

	function _list() {
		$user = $this->f3->get("user");

		$section = 'watchlist';
		$av_section = 'companies';
		$settings = models\settings::_read($section,$av_section);
		
//test_array($settings); 
		$grouping_g = (isset($_REQUEST['group'])&& $_REQUEST['group']!="") ? $_REQUEST['group'] : $settings['group']['g'];
		$grouping_d = (isset($_REQUEST['groupOrder']) && $_REQUEST['groupOrder'] != "") ? $_REQUEST['groupOrder'] : $settings['group']['o'];

		$ordering_c = (isset($_REQUEST['order']) && $_REQUEST['order'] != "") ? $_REQUEST['order'] : $settings['order']['c'];
		$ordering_d = $settings['order']['o'];

		$highlight = (isset($_REQUEST['highlight']) && $_REQUEST['highlight'] != "") ? $_REQUEST['highlight'] : $settings['highlight'];
		$filter = (isset($_REQUEST['filter']) && $_REQUEST['filter']!="") ? $_REQUEST['filter'] : $settings['filter'];
		
		

		if ((isset($_REQUEST['order']) && $_REQUEST['order'] != "")){
			if ($settings['order']['c'] == $_REQUEST['order']){
				if ($ordering_d=="ASC"){
					$ordering_d = "DESC";
				} else {
					$ordering_d = "ASC";
				}

			}

		}


		$watched = (isset($_REQUEST['watched']) && $_REQUEST['watched']!="") ? $_REQUEST['watched'] : $settings['watched'];
		$range = (isset($_REQUEST['range']) && $_REQUEST['range']!="") ? $_REQUEST['range'] : $settings['range'];
		$search = (isset($_REQUEST['search']) && $_REQUEST['search']!="") ? $_REQUEST['search'] : "";





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
			"watched"=>$watched,
			"range"=>$range,
			"search"=>$search,

		);

		
		
		
	

		
		

		models\settings::save($values);

		$DefaultSettings = \apps\cm\settings::_available("",$av_section);
		if (isset($DefaultSettings['columns'][$ordering['c']])) {
			$ordering['c'] = isset($DefaultSettings['columns'][$ordering['c']]['o']) ? $DefaultSettings['columns'][$ordering['c']]['o'] : $ordering['c'];
		} else {
			$ordering['c'] = "company";
		}
		

		

		$range_label = $DefaultSettings;


		$where = "cID = '".$user['company']['ID']."' AND cm_watchlist_companies.uID='{$user['ID']}'";
		
		if ($watched=='1'){
			$where = $where  . " AND cm_watchlist_companies.uID is not null";
		} else if ($watched=='0'){
			$where = $where  . " AND cm_watchlist_companies.uID is null";
		}

		$range_label = "";
		if (isset($DefaultSettings['general']['activity_range'][$range])){
			$days = $DefaultSettings['general']['activity_range'][$range]['days'];
			$range_label = $DefaultSettings['general']['activity_range'][$range]['label'];

			if ($range=="0"){
				
			} else {
				if ($range=="10"){
					$where = $where  . " AND GREATEST(COALESCE(c_max_int.lastInteraction, '0000-00-00'),COALESCE(c_max_note.lastNote, '0000-00-00')) = '0000-00-00'";
				} else {
					$where = $where  . " AND DATEDIFF(now(), REPLACE(GREATEST(COALESCE(c_max_int.lastInteraction, '0000-00-00'),COALESCE(c_max_note.lastNote, '0000-00-00')),'0000-00-00','')) >= ".$days;
				}
				
			}
			
		}
		
		if ($search){
			$IDs = $this->f3->get("DB")->exec("SELECT cm_companies_details.parentID FROM  `cm_companies_details` INNER JOIN cm_companies ON cm_companies_details.parentID = cm_companies.ID WHERE  `value` LIKE  '%{$search}%' AND cm_companies.cID = '".$user['company']['ID']."'");
			$IDstring = array();
			foreach ($IDs as $item){
				if (!in_array($item['parentID'],$IDstring))	$IDstring[] = $item['parentID'];
			}
			$IDstring = implode(",",$IDstring);


			$where_search = "";
			if ($IDstring){
				$where_search = " OR c.ID in ({$IDstring}) ";
			}
			
			$where = $where. " AND (c.company LIKE '%{$search}%' OR c.short LIKE '%{$search}%' $where_search )";
			
		}

		
	
		
		//$where = $where . "AND cm_watchlist_companies.uID =";

		$records = models\companies::getList($where, $grouping, $ordering);

		//test_array($where); 
		$return = array();

		$return['range'] = $range;
		$return['range_label'] = $range_label;
		$return['group'] = $grouping;
		$return['order'] = array("c" => $ordering_c, "o" => $ordering_d);


		$return['list'] = models\companies::display_list($records, array("highlight"=>$highlight,"filter"=>$filter));

		//test_array($this->f3->get("json"));
		return $GLOBALS["output"]['data'] = $return;
	}

}
