<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\nf\controllers\data;

use \timer as timer;
use \apps\nf\models as models;
use \models\user as user;


class deleted extends data {
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

		$section = 'deleted';
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
		$orderby = " title ASC";
		$arrange = "";

		$searchsql = "";
		if ($search_string){
			$search_string = mysql_escape_string($search_string);
			$searchsql .= " AND (title like '%$search_string%' OR nf_categories.category like '%$search_string%' OR global_users.fullName like '%$search_string%' OR nf_article_types.type like '%$search_string%' OR meta like '%$search_string%') ";
			$options['body_search'] = $search_string;
		}
		if ($search_dates){
			$search_dates = explode("to",$search_dates);

			if (count($search_dates)==1){
				$searchsql .= " AND (nf_articles.datein = '".trim($search_dates[0])."' OR deleted_date = '".trim($search_dates[0])."') ";
			} else {
				$searchsql .= " AND ((nf_articles.datein >= '" . trim($search_dates[0])."' AND nf_articles.datein <= '" . trim($search_dates[1])."') OR (nf_articles.deleted_date >= '" . trim($search_dates[0])."' AND nf_articles.deleted_date <= '" . trim($search_dates[1])."')) ";
			}

		}


		$where = "(nf_articles.cID = '$cID') AND deleted ='1' $searchsql";
		if (($user['permissions']['view']['only_my_records'] == '1')) {
			$where = $where . " AND authorID = '" . $user['ID'] . "'";
		}


		$selectedpage = (isset($_REQUEST['page'])) ? $_REQUEST['page'] :"";
		if (!$selectedpage) $selectedpage = 1;

		$recordsFound = models\articles::getAll_count($where,$options);


		//$selectedpage = 2;
		//$recordsFound = 55;

		$limits = array();;
		if ($nolimits==true) {

			$pagination = array();
		} else {
			$limit = 100;
			$pagination = new \pagination();
			$pagination = $pagination->calculate_pages($recordsFound, $limit,$selectedpage, 19);

			//test_array($pagination);

			$limits = $pagination['limit'];


		}

		//test_array($ordering); 


		$options['limit']=$limits;


		$records = models\articles::getAll($where, $grouping, $ordering, $options);




		$return = array();



		$return['group'] = $grouping;
		$return['order'] = array("c" => $ordering_c, "o" => $ordering_d);
		$return['pagination'] = $pagination;

		$return['stats']['records']= $recordsFound;




		$return['list'] = models\articles::display($records);

		return $GLOBALS["output"]['data'] = $return;
	}

}
