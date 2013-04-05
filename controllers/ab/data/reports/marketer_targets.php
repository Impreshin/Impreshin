<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace controllers\ab\data\reports;
use \F3 as F3;
use \timer as timer;
use \models\ab as models;
use \models\user as user;


class marketer_targets extends \controllers\ab\data\data {
	function __construct() {
		$this->f3 = \base::instance();
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		if (!$userID) exit(json_encode(array("error" => $this->f3->get("system")->error("U01"))));

	}

	function _data() {
		$timer = new timer();

		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];

		$cID = $user['publication']['cID'];
		$section = "reports_marketer";
		$return = array();

		$settings = models\settings::_read($section);

		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";


		if (isset($user['marketer']['ID'])&&$user['marketer']['ID']&&$user['permissions']['reports']['marketer']['targets']['page']!='1') $ID = $user['marketer']['ID'];


		$values = array();

		$values[$section] = array(
			"years"=> $settings['years'],
			"timeframe"=> $settings['timeframe'],
			"combined"=> $settings['combined'],
			"order"=> $settings['order'],
			"tolerance"=> $settings['tolerance'],
		);
		$values[$section]['ID']["cID_$cID"] = $ID;

		models\user_settings::save_setting($values);


		$selectedpage = (isset($_REQUEST['page'])) ? $_REQUEST['page'] : "";
		$rows = (isset($_REQUEST['rows'])) ? $_REQUEST['rows'] : "";
		if (!$selectedpage) $selectedpage = 1;


		$where = "mID = '$ID' AND date_to AND date_from";

		$recordsFound = count(models\marketers_targets::getAll($where));

		$limit = $rows;
		$pagination = new \pagination();
		$pagination = $pagination->calculate_pages($recordsFound, $limit, $selectedpage, 19);

		//test_array($pagination);

		$limits = $pagination['limit'];

		$targets = models\marketers_targets::getAll($where, "date_to DESC", $limits);


		$select = "sum(totalcost) as totalcost, count(ab_bookings.ID) as records";
		$where = "ab_bookings.marketerID = '$ID' ";

		$t = array();
		foreach ($targets as $target){
			$target['date_from_D'] = date("d F Y",strtotime($target['date_from']));
			$target['date_to_D'] = date("d F Y",strtotime($target['date_to']));
			$target['target_C'] = currency($target['target']);

			$pubs = $target['pubs'];
			$pubs = explode(",",$pubs);

			if (in_array($pID,$pubs)){
				$records = models\bookings::getAll_select($select, $where . "AND (global_dates.publish_date >= '" . $target['date_from'] . "' AND global_dates.publish_date <= '" . $target['date_to'] . "') AND ab_bookings.pID in (" . $target['pubs'] . ") AND deleted is null AND checked ='1'", "global_dates.publish_date ASC ", "ab_bookings.marketerID");


				//test_array(array("targets"=>$target,"records"=>$records));
				if (count($records)) {
					$records = $records[0];
					$records['totalcost_C'] = currency($records['totalcost']);
					$target['total'] = $records;
					$target['percent'] = number_format(($records['totalcost'] / $target['target']) * 100, 2);
				} else {
					$records['totalcost_C'] = currency(0);
					$target['total'] = array(
						"totalcost" => 0,
						"records"   => 0
					);
					$target['percent'] = number_format(0, 2);
				}
				$editable = "0";
				//test_array($user);
				if (isset($user['ab_marketerID']) && $user['ab_marketerID'] == $ID){
					if ($target['locked']!='1'){
						$editable = '1';
					}
				}
				if ($user['permissions']['administration']['application']['marketers']['targets']['page']){
					$editable = '1';
				}

				$target['editable'] = $editable;



				$t[] = $target;
			}


		}
		$targets = $t;


		//test_array($targets);
		$data = array();
		$headings = array("f"=>"Future","p"=>"Past","a"=>"Active");
		foreach ($targets as $target){
			$d = "";
			if ($target['date_from'] > date("Y-m-d")) {
				$d = "f";
			}
			if ($target['date_to'] < date("Y-m-d")) {
				$d = "p";
			}
			if ($target['date_from'] <= date("Y-m-d") && $target['date_to'] >= date("Y-m-d")){
				$d = "a";
			}

			//test_array(array("from"=> $target['date_from'],"to"=> $target['date_to'],"now"=> date("Y-m-d")));


			if ($d){
				if (!isset($data[$d])) {
					$data[$d]['heading'] = $headings[$d];
					$data[$d]['targets'] = array();
				}
				$data[$d]['targets'][] = $target;
			}

		}
		$return['pagination'] = $pagination;
		$return['targets'] = array_values($data);

		$timer->stop("Report - ". __CLASS__ . "->" .  __FUNCTION__ );
		return $GLOBALS["output"]['data'] = $return;
	}

	function _details() {

		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['publication']['ID'];
		$cID = $user['publication']['cID'];
		//test_array($cID);

		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";
		$mID = (isset($_REQUEST['mID'])) ? $_REQUEST['mID'] : (isset($user['marketer']) && isset($user['marketer']['ID']))? $user['marketer']['ID']:"";

		$o = new models\marketers_targets();
		$details = $o->get($ID);

		$ID = $details['ID'];


		$return = array();
		$publications = models\marketers::getPublications($mID);

		


		if (!$details['ID']) {
			$userPublications = array();
		} else {
			$userPublications = $this->f3->get("DB")->exec("SELECT pID FROM ab_marketers_targets_pub WHERE mtID = '$ID'");
		}


		$pstr = array();
		foreach ($userPublications as $u) $pstr[] = $u['pID'];

		$pubarray = array();
		$pIDarray = array();
		foreach ($publications as $pub) {
			$pub['selected'] = 0;
			if (in_array($pub['ID'], $pstr)) {
				$pub['selected'] = 1;
			}
			$pIDarray[] = $pub['ID'];
			$pubarray[] = $pub;
		}

		$publications = $pubarray;
		$return['details'] = $details;
		$return['publications'] = $publications;


		return $GLOBALS["output"]['data'] = $return;
	}


}
