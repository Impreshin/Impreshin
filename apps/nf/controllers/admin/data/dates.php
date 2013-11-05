<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\nf\controllers\admin\data;

use \timer as timer;
use \apps\nf\models as models;
use \models\user as user;


class dates extends \apps\nf\controllers\data\data {
	function __construct() {
		parent::__construct();

	}
	function _list() {

		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];

		$selectedpage = (isset($_REQUEST['page'])) ? $_REQUEST['page'] : "";
		$nrrecords = (isset($_REQUEST['nr'])) ? $_REQUEST['nr'] : 10;

		$section = "admin_dates";

		$settings = models\settings::_read($section);

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


		$values = array();
		$values[$section] = array(
			"order"      => array(
				"c"=> $ordering_c,
				"o"=> $ordering_d
			),

		);

		models\settings::save($values);




		$currentDate = $user['publication']['current_date'];
		$dID = $currentDate['ID'];


		$where = "pID='$pID'";
		$recordsFound = \models\dates::getAll_count($where);
		$limit = $nrrecords;
		$pagination = new \pagination();
		$pagination = $pagination->calculate_pages($recordsFound, $limit, $selectedpage, 7);

		$records = \models\dates::getAll("pID='$pID'", $ordering_c . " " .$ordering_d . ",publish_date DESC", $pagination['limit']);

		$return = array();
		$return['pagination'] = $pagination;
		$return['records'] = $records;

		$GLOBALS["output"]['data'] = $return;
	}
	function _details(){

		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['publication']['ID'];

		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";

		$dates = new \models\dates();
		$dates = $dates->get($ID);
		$dates['current'] = '0';
		if ($user['publication']['current_date']['ID']== $dates['ID'])	$dates['current'] = '1';

		


		$last2 = \models\dates::getAll("pID='$pID'", "publish_date DESC", "0,2");

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
