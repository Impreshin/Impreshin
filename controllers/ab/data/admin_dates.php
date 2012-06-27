<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace controllers\ab\data;
use \F3 as F3;
use \timer as timer;
use \models\ab as models;
use \models\user as user;


class admin_dates extends data {
	function __construct() {

		$user = F3::get("user");
		$userID = $user['ID'];
		if (!$userID) exit(json_encode(array("error" => F3::get("system")->error("U01"))));

	}
	function _list() {
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];

		$selectedpage = (isset($_REQUEST['page'])) ? $_REQUEST['page'] : "";
		$nrrecords = (isset($_REQUEST['nr'])) ? $_REQUEST['nr'] : 10;

		$currentDate = $user['publication']['current_date'];
		$dID = $currentDate['ID'];


		$where = "pID='$pID'";
		$recordsFound = models\dates::getAll_count($where);
		$limit = $nrrecords;
		$pagination = new \pagination();
		$pagination = $pagination->calculate_pages($recordsFound, $limit, $selectedpage, 7);

		$records = models\dates::getAll("pID='$pID'","publish_date DESC", $pagination['limit']);

		$return = array();
		$return['pagination'] = $pagination;
		$return['records'] = $records;

		$GLOBALS["output"]['data'] = $return;
	}
	function _details(){
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['publication']['ID'];

		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";

		$dates = new models\dates();
		$dates = $dates->get($ID);
		$dates['current'] = '0';
		if ($user['publication']['current_date']['ID']== $dates['ID'])	$dates['current'] = '1';

		if ($dates['ID']){
			$pID = $dates['pID'];
			$recordsFound = models\bookings::getAll_count("ab_bookings.dID = $ID");
		} else {
			$recordsFound = 0;
		}

		$dates['records'] = $recordsFound;


		$last2 = models\dates::getAll("pID='$pID'", "publish_date DESC", "0,2");

		if (count($last2)==2){
			$last_0 = new \DateTime($last2[0]['publish_date']);
			$last_1 = new \DateTime($last2[1]['publish_date']);

			$interval = $last_0->diff($last_1);
			$diff = $interval->format('%d');
			$suggestions = array();
			//$suggestions[] = date("Y-m-d", $last, " +$diff day");
			$date = $last2[0]['publish_date'];
			$date = date("Y-m-d", strtotime(date("Y-m-d", strtotime($date)) . " +$diff day"));

			$suggestions[] = array(
				"display"=> date("d F Y", strtotime($date)),
				"date"=> $date
			);
			$date = date("Y-m-d", strtotime(date("Y-m-d", strtotime($suggestions[count($suggestions) - 1]['date'])) . " +$diff day"));
			$suggestions[] = array(
				"display"=> date("d F Y", strtotime($date)),
				"date"=> $date
			);
			$date = date("Y-m-d", strtotime(date("Y-m-d", strtotime($suggestions[count($suggestions) - 1]['date'])) . " +$diff day"));
			$suggestions[] = array(
				"display"=> date("d F Y", strtotime($date)),
				"date"=> $date
			);
			$date = date("Y-m-d", strtotime(date("Y-m-d", strtotime($suggestions[count($suggestions) - 1]['date'])) . " +$diff day"));
			$suggestions[] = array(
				"display"=> date("d F Y", strtotime($date)),
				"date"=> $date
			);



		} else {
			$suggestions = array();
		}



		$dates['suggestions'] = $suggestions;



		$GLOBALS["output"]['data'] = $dates;
	}

}
