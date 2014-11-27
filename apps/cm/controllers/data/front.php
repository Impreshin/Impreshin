<?php

namespace apps\cm\controllers\data;

use apps\cm\models as models;


class front extends data {
	function __construct() {
		parent::__construct();


	}

	function feed() {
		$user = $this->f3->get("user");

		$section = 'front';
		$settings = models\settings::_read($section);

		$feed_days = (isset($_REQUEST['feed_days']) && $_REQUEST['feed_days'] != "") ? $_REQUEST['feed_days'] : $settings['feed_days'];

		$values = array();
		$values[$section] = array(
			'feed_days' => $feed_days

		);





		models\settings::save($values);

		$DefaultSettings = \apps\cm\settings::_available("");

		$where = "cID = '" . $user['company']['ID'] . "' AND cm_watchlist_companies.uID='{$user['ID']}'";

		if ($feed_days) {
			$dateback = date('Y-m-d', strtotime('-' . $feed_days . ' days'));
			$where = $where . " AND DATE_FORMAT(datein, '%Y-%m-%d') >= '$dateback'";


		}



		$records = models\feed::getList($where, "datein DESC");

		$t = array();
		foreach ($records as $item) {
			if (!isset($t[$item['parentID']])) {
				$t[$item['parentID']] = array(
					"heading" => $item['heading'], "linkID" => $item['linkID'], "records" => array()
				);
			}
			$t[$item['parentID']]['records'][] = $item;
		}
		$n = array();
		foreach ($t as $item) {
			$n[] = $item;
		}
		$ticker = $n;



		//	$ticker = $t;

		$return['data'] = $ticker;

		//test_array($this->f3->get("json"));
		return $GLOBALS["output"]['data'] = $return;
	}

	function left() {
		$user = $this->f3->get("user");
		$return = array();
		$section = 'front';
		$settings = models\settings::_read($section);

		$tab = (isset($_REQUEST['tab']) && $_REQUEST['tab'] != "") ? $_REQUEST['tab'] : $settings['tab'];

		$values = array();
		$values[$section] = array(
			'tab' => $tab

		);



		SWITCH ($tab) {
			CASE "1":
				$return = $this->left_1();
				break;
			CASE "2":
				$return = $this->left_2();
				break;
			CASE "3":
				$return = $this->left_2();
				break;
			CASE "4":
				$return = $this->left_2();
				break;


		}

		$return['tab'] = $tab;

		models\settings::save($values);



		//test_array($this->f3->get("json"));
		return $GLOBALS["output"]['data'] = $return;
	}

	function left_1() {
		$user = $this->f3->get("user");
		$return = array();
		$section = 'front';
		$settings = models\settings::_read($section);

		$start = (isset($_REQUEST['start']) && $_REQUEST['start'] != "") ? $_REQUEST['start'] : strtotime('now');
		$end = (isset($_REQUEST['end']) && $_REQUEST['end'] != "") ? $_REQUEST['end'] : "";
		
		$t = array(
			array(
				"title" => "All Day Event", 
				"start" => '2014-11-01'
			), 
			array(
				"title" => 'Long Event', 
				"start" => '2014-11-07', 
				"end" => '2014-11-10'
			), 
			array(
				"id" => 999, 
				"title" => 'Repeating Event', 
				"start" => '2014-11-09T16:00:00'
			), 
			array(
				"id" => 999, 
				"title" => 'Repeating Event', 
				"start" => '2014-11-17T16:00:00'
			), 
			array(
				"title" => 'Conference', 
				"start" => '2014-11-11', 
				"end" => '2014-11-13'
			), 
			array(
				"title" => 'Meeting', 
				"start" => '2014-11-12T10:30:00', 
				"end" => '2014-11-12T12:30:00'
			), 
			array(
				"title" => 'Lunch', 
				"start" => '2014-11-12T12:00:00'
			),
			array(
				"title" => 'Meeting', 
				"start" => '2014-11-12T14:30:00'
			), 
			array(
				"title" => 'Happy Hour', 
				"start" => '2014-11-12T17:30:00'
			), 
			array(
				"title" => 'Dinner', 
				"start" => '2014-11-12T20:00:00'
			),
			array(
				"title" => 'This is a heading',
				"start" => '2014-11-12T20:00:00',
				"class" => 'notes',
				"icon"=>'icon-flag',
				"type"=>'1'
			),
			array(
				"title" => 'This is a heading 2',
				"start" => '2014-11-12T03:00:00',
				"class" => 'interaction',
				"icon"=>'icon-envelope',
				"type"=>'1'
			)
		);



		$return['start'] = $start;
		$return['end'] = $end;

		$return['records'] = $t;


		return $return;
	}

	function left_2() {
		$user = $this->f3->get("user");
		$return = array();
		$section = 'front';
		$settings = models\settings::_read($section);


		$return['records'] = array("t");


		return $return;
	}

	function left_3() {
		$user = $this->f3->get("user");
		$return = array();
		$section = 'front';
		$settings = models\settings::_read($section);


		$return['records'] = array("t");


		return $return;
	}

	function left_4() {
		$user = $this->f3->get("user");
		$return = array();
		$section = 'front';
		$settings = models\settings::_read($section);


		$return['records'] = array("t");


		return $return;
	}

	function search() {
		$user = $this->f3->get("user");
		$return = array();
		$section = 'front';
		$settings = models\settings::_read($section);
		$search = (isset($_REQUEST['search']) && $_REQUEST['search'] != "") ? $_REQUEST['search'] : "";
		//$values = array();
		//$values[$section]['search'] = $search;

		//models\settings::save($values);

		$return['search'] = $search;
		$return['records'] = array();


		return $GLOBALS["output"]['data'] = $return;
	}

}
