<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\ab\controllers\data\reports;

use \timer as timer;
use \apps\ab\models as models;
use \models\user as user;
use \apps\ab\controllers\data\data as data;

class publication_placing_figures extends data {
	function __construct() {
		parent::__construct();

	}

	function _data() {
		$timer = new timer();

		$user = $this->f3->get("user");
		$userID = $user['ID'];


		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];

		$section = "reports_publication_placing";
		$return = array();

		$settings = models\settings::_read($section);


		$publications = $pID;
		$years = isset($_REQUEST['years']) ? $_REQUEST['years'] : "";
		$daterange = isset($_REQUEST['daterange']) ? $_REQUEST['daterange'] : "";
		$combined = isset($_REQUEST['combined']) ? $_REQUEST['combined'] : $settings['combined'];
		$tolerance = isset($_REQUEST['tolerance']) ? $_REQUEST['tolerance'] : $settings['tolerance'];
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";
		$dID = isset($_REQUEST['dID']) ? $_REQUEST['dID'] : "";

		$grouping_g =  $settings['group']['g'];
		$grouping_d =  $settings['group']['o'];
		$ordering_c = (isset($_REQUEST['order']) && $_REQUEST['order'] != "") ? $_REQUEST['order'] : $settings['order']['c'];
		$ordering_d = $settings['order']['o'];
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
		if ($combined=='none'){
			$combined = $settings['combined'];
		}
		if ($ID==''){
			$ID = (isset($settings['ID']["cID_$cID"])) ? $settings['ID']["cID_$cID"] : "";
		}

		$tab = "charts";
		if ($dID){
			$tab = "records";
		}


		$return['tab']=$tab;
		$return['dID']=$dID;
		$return['tolerance']=$tolerance;
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

		}
		;
		$daterange_s = explode(" to ", $daterange_s);


		$years_d = $this->f3->get("DB")->exec("SELECT distinct year(publish_date) AS record_year FROM global_dates WHERE pID in ($publications) ORDER BY year(publish_date) DESC");


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



		$values = array();
		$values[$section] = array(
			"years"=> $years,
			"timeframe"=> $daterange,
			"combined"=> $combined,
					"order"=> $ordering,
					"tolerance"=> $tolerance,
		);
		$values[$section]['ID']["pID_$pID"] = $ID;





		models\settings::save($values);





		$y = array();
		$years = explode(",", $years);
		$yearsSend = array();
		foreach ($years_d as $year){
			$y[] = array(
				"y"=>$year['record_year'],
				"s"=>(in_array($year['record_year'], $years))?"1":"0"
			);
			if (in_array($year['record_year'], $years)){
				$yearsSend[] = $year['record_year'];
			}
		}
		$yearsSend_str = implode(",", $yearsSend);
		if ($yearsSend_str=="")	$yearsSend_str = "null";



		$years = ($y);;
		$where_general = "checked = '1' AND ab_bookings.placingID = '$ID' AND deleted is null";
		if ($tab=="charts"){
			$where = $where_general;
			if (!isset($daterange_s[0])) {
				$daterange_s[0] = date("Y-m-01", strtotime('-12 month'));
			}
			if (!isset($daterange_s[1])) {
				$daterange_s[1] = date($daterange_s[0], strtotime('-1 month'));
			}
			sort($daterange_s);
			$return['lines'] = models\report_figures::lines($where,array("from"=>date("Y-m-d",strtotime($daterange_s[0])),"to"=> date("Y-m-d",strtotime($daterange_s[1]))), $publications);
		}
		if ($tab=="records"){
					$orderby = " client ASC";
					$arrange = "";
					$where = "(ab_bookings.dID='$dID') AND $where_general";
					$records = models\bookings::getAll($where, $grouping, $ordering);
			$return['records'] = models\bookings::display($records);

		}
		$return['comp']['years']=$years;
		$where = "ab_bookings.pID in ($publications) AND year(global_dates.publish_date) in ($yearsSend_str) AND $where_general";
		$return['comp']['data'] = models\report_figures::figures($where, $yearsSend, $tolerance);


		$date_range = $this->f3->get("DB")->exec("SELECT min(publish_date) as earliestDate, max(publish_date) as latestDate FROM global_dates WHERE pID  in ($publications)");
		if (count($date_range)) {
			$date_range = $date_range[0];
		}



		$return['pubs'] = count(explode(",",$publications));
		$return['daterange'] = $daterange;
		$return['combined'] = $combined;
		$return['date_min'] = $date_range['earliestDate'];
		$return['date_max'] = $date_range['latestDate'];


		$timer->stop("Report - ". __CLASS__ . "->" .  __FUNCTION__ );
		return $GLOBALS["output"]['data'] = $return;
	}


}
