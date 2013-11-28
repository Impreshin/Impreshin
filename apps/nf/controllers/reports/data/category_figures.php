<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\nf\controllers\reports\data;

use \timer as timer;
use \apps\nf\models as models;
use \models\user as user;
use \apps\nf\controllers\data\data as data;

class category_figures extends data {
	function __construct() {
		parent::__construct();

	}

	function _data() {
		$timer = new timer();

		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];
		$section = "reports_category_figures";
		$return = array();

		$settings = models\settings::_read($section);




		$years = isset($_REQUEST['years']) ? $_REQUEST['years'] : "";
		$daterange = isset($_REQUEST['daterange']) ? $_REQUEST['daterange'] : "";
		$filter = (isset($_REQUEST['filter']) && $_REQUEST['filter']!="") ? $_REQUEST['filter'] : $settings['filter'];
		$tolerance = isset($_REQUEST['tolerance']) ? $_REQUEST['tolerance'] : $settings['tolerance'];
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";
		$dID = isset($_REQUEST['dID']) ? $_REQUEST['dID'] : "";

		$grouping_g = (isset($_REQUEST['group'])&& $_REQUEST['group']!="") ? $_REQUEST['group'] : $settings['group']['g'];
		$grouping_d = (isset($_REQUEST['groupOrder']) && $_REQUEST['groupOrder'] != "") ? $_REQUEST['groupOrder'] : $settings['group']['o'];
		$ordering_c = (isset($_REQUEST['order']) && $_REQUEST['order'] != "") ? $_REQUEST['order'] : $settings['order']['c'];
		$ordering_d = $settings['order']['o'];
		if ((isset($_REQUEST['order']) && $_REQUEST['order'] != "")) {
			if ($settings['order']['c'] == $_REQUEST['order']) {
				if ($ordering_d == "ASC") {
					$ordering_d = "DESC";
				} else {
					$ordering_d = "ASC";
				}
			}
		}

		
		if ($ID == '') {
			$ID = (isset($settings['ID']["cID_$cID"])) ? $settings['ID']["cID_$cID"] : "";
		}

		$tab = "charts";
		if ($dID) {
			$tab = "records";
		}


		$return['tab'] = $tab;
		$return['dID'] = $dID;
		$return['tolerance'] = $tolerance;
		if (!$daterange) {
			$daterange = $settings['timeframe'];
			if (!$daterange) {
				$daterange = "12m";
			}
		}

		$daterange_s = $daterange;
		switch ($daterange) {
			case "6m":
				$daterange_s = date("Y-m-01", strtotime('-6 month')) . " to " . date("Y-m-t", strtotime('-1 month'));
				break;
			case "12m":
				$daterange_s = date("Y-m-01", strtotime('-12 month')) . " to " . date("Y-m-t", strtotime('-1 month'));
				break;
			case "24m":
				$daterange_s = date("Y-m-01", strtotime('-24 month')) . " to " . date("Y-m-t", strtotime('-1 month'));
				break;

		};
		$daterange_s = explode(" to ", $daterange_s);


		$years_d = $this->f3->get("DB")->exec("SELECT DISTINCT year(datein) AS record_year FROM nf_articles WHERE categoryID = '$ID' ORDER BY year(datein) DESC");


		//($settings['years'])? $settings['years']:$years_d[0]['record_year'];
		if (!$years) {
			$years = $settings['years'];
			if (!$years) {
				$years = array();
				$i = 0;
				foreach ($years_d as $d) {
					if ($i++ < 3) $years[] = $d['record_year'];
				}
				$years = implode(",", $years);
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
			"years" => $years, 
			"timeframe" => $daterange,
			"filter"=>$filter,
			"group"=> $grouping,
			"order" => $ordering, 
			"tolerance" => $tolerance,
		);
		$values[$section]['ID']["cID_$cID"] = $ID;



	//	test_array($values);



		models\settings::save($values);





		$y = array();
		$years = explode(",", $years);
		$yearsSend = array();
		foreach ($years_d as $year) {
			$y[] = array("y" => $year['record_year'], "s" => (in_array($year['record_year'], $years)) ? "1" : "0");
			if (in_array($year['record_year'], $years)) {
				$yearsSend[] = $year['record_year'];
			}
		}
		$yearsSend_str = implode(",", $yearsSend);

		if ($yearsSend_str=='')$yearsSend_str = date("Y");

		



		$years = ($y);;

		$placed_sql = "";
		SWITCH ($filter){
			CASE '1':
				$placed_sql = " AND nf_article_newsbook.placed='1' ";
				break;
			CASE '0':
				$placed_sql = " AND nf_article_newsbook.placed='0' ";
				break;
		}

		$where_general_gen = "categoryID = '$ID' AND deleted is null";
		$where_general = $where_general_gen . $placed_sql . " AND nf_articles.rejected !='1' AND (SELECT count(p_nb.ID) FROM nf_article_newsbook p_nb WHERE p_nb.aID = nf_articles.ID AND p_nb.placed='1') > 0 AND global_publications.ID = '$pID'";
		//test_array(array("where"=>$where_general,"range"=>array("from"=>date("Y-m-d",strtotime($daterange_s[0])),"to"=> date("Y-m-d",strtotime($daterange_s[1]))), "pubs"=>$publications));
		if ($tab == "charts") {
			$where = $where_general;
			if (!isset($daterange_s[0])) {
				$daterange_s[0] = date("Y-m-01", strtotime('-12 month'));
			}
			if (!isset($daterange_s[1])) {
				$daterange_s[1] = date($daterange_s[0], strtotime('-1 month'));
			}
			sort($daterange_s);
			
			$return['lines'] = models\report_figures_newsbook::lines($where, array("from" => date("Y-m-d", strtotime($daterange_s[0])), "to" => date("Y-m-d", strtotime($daterange_s[1]))));
		}




		if ($tab == "records") {
			$orderby = " title ASC";
			$arrange = "";
			$where = "$where_general AND global_dates.ID = '$dID' ";
			
		//	test_array($grouping);

		//	test_array(array($where, $grouping, $ordering));
			$records = models\articles::getAll($where, $grouping, $ordering, array("pID"=>$pID,"distinct"=>"nf_articles.ID, nf_article_newsbook.pID, nf_article_newsbook.dID","select"=>" nf_article_newsbook.ID AS newsbookID, nf_article_newsbook.pID, global_dates.publish_date, global_publications.publication, nf_article_newsbook.placed AS placed"));
			//$d = articles::getAll($where, "","",array("pID"=>$pID,"distinct"=>"nf_articles.ID, nf_article_newsbook.pID, nf_article_newsbook.dID","select"=>" nf_article_newsbook.pID, global_dates.publish_date, global_publications.publication"));
			
			
			$return['records'] = models\articles::display($records);

			//	test_array($return['records']);

		}
		$return['comp']['years'] = $years;
		$where = " year(nf_articles.datein) in ($yearsSend_str) AND $where_general";
		$return['comp']['data'] = models\report_figures_newsbook::figures($where, $yearsSend, $pID, $tolerance);




		$date_range = $this->f3->get("DB")->exec("SELECT min(datein) AS earliestDate, max(datein) AS latestDate FROM nf_articles WHERE $where_general_gen");
		if (count($date_range)) {
			$date_range = $date_range[0];
		}




		$return['daterange'] = $daterange;
		$return['filter'] = $filter;
		$return['date_min'] = $date_range['earliestDate'];
		$return['date_max'] = $date_range['latestDate'];


		$timer->stop("Report - " . __CLASS__ . "->" . __FUNCTION__);

		return $GLOBALS["output"]['data'] = $return;
	}


}
