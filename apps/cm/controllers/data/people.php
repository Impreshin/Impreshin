<?php

namespace apps\cm\controllers\data;

use \timer as timer;
use \apps\cm\models as models;
use \models\user as user;


class people extends data {
	function __construct() {
		parent::__construct();


	}

	function _list() {
		$user = $this->f3->get("user");

		$section = 'contacts';
		$av_section = 'contacts';
		$settings = models\settings::_read($section,$av_section);

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

		);
		

		
		

		models\settings::save($values);

		$DefaultSettings = \apps\cm\settings::_available("",$av_section);
		if (isset($DefaultSettings['columns'][$ordering['c']])) {
			$ordering['c'] = isset($DefaultSettings['columns'][$ordering['c']]['o']) ? $DefaultSettings['columns'][$ordering['c']]['o'] : $ordering['c'];
		} else {
			$ordering['c'] = "company";
		}



		$where = "cID = '".$user['company']['ID']."'";


		$records = models\contacts::getAll($where, $grouping, $ordering);

		$return = array();

		$return['group'] = $grouping;
		$return['order'] = array("c" => $ordering_c, "o" => $ordering_d);


		$return['list'] = models\contacts::display_list($records, array("highlight"=>$highlight,"filter"=>$filter));

		//test_array($this->f3->get("json"));
		return $GLOBALS["output"]['data'] = $return;
	}

}
