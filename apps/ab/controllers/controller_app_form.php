<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace apps\ab\controllers;

use models\global_colours;
use \timer as timer;
use \apps\ab\models as models;

class controller_app_form extends \apps\ab\controllers\_{
	function __construct() {
		parent::__construct();
	}
	function page_new(){
//test_array($user);
		if ($this->user['permissions']['form']['new']) {
			$this->page();
		} else {
			$this->f3->error(404);
		}

	}
	function page_edit(){
		if ($this->user['permissions']['form']['edit'] || $this->user['permissions']['form']['edit_master'] || $this->user['permissions']['form']['delete']) {
			$this->page();
		} else {
			$this->f3->error(404);
		}
	}

	function page(){
		$ID = $this->f3->get('PARAMS["ID"]');
		$user = $this->user;
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];

		$cfg = $this->f3->get("CFG");

		$userID = $user['ID'];
		$currentDate = $user['publication']['current_date'];
		$dID = $currentDate['ID'];


		$detailsO = new models\bookings();
		$details = $detailsO->get($ID);
		//test_array($details);
		if ($details['ID'] && ($details['pID'] != $pID)) {
			$details = $detailsO->dbStructure();
		}




		$dates = \models\dates::getAll("pID='$pID' AND publish_date > '" . $currentDate['publish_date'] . "'", "publish_date ASC", "");
		$selectedDate = new \models\dates();
		$selectedDate = $selectedDate->get($details['dID']);

		$d = array();
		foreach ($dates as $date) {
			$d[] = $date['ID'];
		}

		if ($selectedDate['ID'] == $currentDate['ID'] || in_array($selectedDate['ID'], $d)) {
			$selectedDate = array();
		}

		$accCount = models\accounts::getAll_count("pID='$pID' AND ab_accounts.cID='$cID'");

		$showsearch = false;
		$limit = "";
		if ($accCount> $cfg['form']['max_accounts']){
			$showsearch = true;
			$limit = "0,". $cfg['form']['max_accounts'];

		}

		$accounts = models\accounts::getAll("pID='$pID' AND ab_accounts.cID='$cID'", "last_used DESC, account ASC",$limit);
		$b = array();
		foreach ($accounts as $account) {
			$b[] = array(
				"ID"      => $account['ID'],
				"accNum"  => $account['accNum'],
				"account" => $account['account'],
				"record"  => array(
					"status"     => $account['status'],
					"blocked"    => $account['blocked'],
					"labelClass" => $account['labelClass'],
					"remark"     => $account['remark']
				),
				"label"   => $account['accNum'],
				"value"   => $account['accNum']
			);
		}

		$accountData = ($b);



		$tmpl = new \template("template.tmpl", "apps/ab/ui/");
		$tmpl->page = array(
			"section"     => "form",
			"sub_section" => "form",
			"template"    => "page_app_form",
			"meta"        => array(
				"title" => "AB - Form - loading..",
			),
			"help"        => "/apps/ab/help/form"
		);

		$tmpl->dates = array(
			"selected" => $selectedDate,
			"current"  => $currentDate,
			"future"   => $dates
		);
		$tmpl->repeat_dates = \models\dates::getAll("pID='$pID' AND publish_date >= '" . $currentDate['publish_date'] . "'", "publish_date ASC", "");
		$tmpl->bookingTypes = models\bookingTypes::getAll("", "orderby ASC");
		$tmpl->remarkTypes = models\remarkTypes::getAll("");
		$tmpl->marketers  = models\marketers::getAll("pID='$pID' AND ab_marketers.cID='$cID'", "marketer ASC");
		$tmpl->production = models\production::getAll("pID='$pID' AND ab_production.cID='$cID'", "production ASC");
		$tmpl->categories = models\categories::getAll("pID='$pID' AND ab_categories.cID='$cID'", "orderby ASC");
		$tmpl->inserts_types = models\inserts_types::getAll("pID='$pID'", "orderby ASC", "");
		$tmpl->placing = models\placing::getAll("pID='$pID'", "orderby ASC", "");
		$tmpl->sub_placing = models\sub_placing::getAll("pID='$pID'", "orderby ASC", "");
		$tmpl->payment_methods = \models\_system::payment_methods_getAll();
		$tmpl->accounts = $accounts;
		$tmpl->showsearch = $showsearch;
		$tmpl->ID = $details['ID'];
		$tmpl->output();

	}
	function _page() {
		$ID = $this->f3->get('PARAMS["ID"]');
		$user = $this->f3->get("user");
		if (!$user['permissions']['form']['new']&& !$user['permissions']['form']['edit']&&!$user['permissions']['form']['edit_master']&& !$user['permissions']['form']['delete']) $this->f3->error(404);
		$userID = $user['ID'];
		$pID = $user['publication']['ID'];
		$cID = $user['publication']['cID'];

		$currentDate = $user['publication']['current_date'];
		$dID = $currentDate['ID'];

		$settings = models\settings::_read("form");




		$clientlist = $this->f3->get("DB")->exec("
			SELECT Distinct client FROM ab_bookings INNER JOIN global_dates ON ab_bookings.dID = global_dates.ID WHERE ab_bookings.pID = '$pID' AND global_dates.publish_date >= DATE_SUB(now(),INTERVAL '60' DAY) ORDER BY global_dates.publish_date DESC LIMIT 0,200
		"
		);
		$a = array();
		foreach ($clientlist AS $client) {
			if ($client['client']) $a[] = $client['client'];
		}

		$clientlist = json_encode($a);


		$accounts = models\accounts::getAll("pID='$pID' AND ab_accounts.cID='$cID'", "account ASC","0,2");


		$marketers = models\marketers::getAll("pID='$pID' AND ab_marketers.cID='$cID'", "marketer ASC");
		$dates = \models\dates::getAll("pID='$pID' AND publish_date > '".$currentDate['publish_date']."'", "publish_date ASC", "");
		$placing = models\placing::getAll("pID='$pID'", "orderby ASC", "");
		$inserts_types = models\inserts_types::getAll("pID='$pID'", "orderby ASC", "");
		$sub_placing = models\sub_placing::getAll("pID='$pID'", "orderby ASC", "");

		$c = array();
		foreach ($sub_placing as $record) {
			if (!isset($c[$record['placingID']])) $c[$record['placingID']]['place'] = "None";
			$c[$record['placingID']]['records'][] = $record;
		}

		foreach ($placing AS $record) {
			if (isset($c[$record['ID']])){
				$c[$record['ID']]['place'] = $record['placing'];
			}
		}

		$sub_placingData = json_encode($c);


		$b = array();
		foreach ($accounts as $record) {
			$b[] = array(
				"accNum" => $record['accNum'],
				"account"=> $record['account'],
				"record" => array(
					"status"    => $record['status'],
					"blocked"   => $record['blocked'],
					"labelClass"=> $record['labelClass'],
					"remark"    => $record['remark']
				),
				"label"  => $record['accNum'],
				"value"  => $record['accNum']
			);
		}

		$accountData = json_encode($b);


//test_array($user);

		$detailsO = new models\bookings();
		$details = $detailsO->get($ID);
		//test_array($details);
		if ($details['ID'] && ($details['pID']!= $pID || $details['deleted']=='1')){
			$details =$detailsO->dbStructure();
		}


		$title = "New Booking";
		if ($details['ID']){
			if (!$user['permissions']['form']['edit'] && !$user['permissions']['form']['edit_master'] && !$user['permissions']['form']['delete']) $this->f3->error(404);
			if ($details['state'] == 'Current' || $details['state'] == 'Future') {
			} else {
				if (!$user['permissions']['form']['edit_master']){
					$this->f3->error(404);
				}

			}

			$title = "".$details['client'];
		} else {
			if (!$user['permissions']['form']['new']) $this->f3->error(404);
		}

		$tmpl = new \template("template.tmpl", "apps/ab/ui/");
		$tmpl->page = array(
			"section"    => "form",
			"sub_section"=> "form",
			"template"   => "page_app_form",
			"meta"       => array(
				"title"=> "AB - Form - $title",
			),
			"help"=>"/apps/ab/help/form"
		);


		$selectedDate = new \models\dates();
		$selectedDate = $selectedDate->get($details['dID']);

		$d = array();
		foreach ($dates as $date){
			$d[] = $date['ID'];
		}

		if ($selectedDate['ID'] == $currentDate['ID'] || in_array($selectedDate['ID'],$d) ){
			$selectedDate = array();
		}
		


		$tmpl->repeat_dates = \models\dates::getAll("pID='$pID' AND publish_date >= '" . $currentDate['publish_date'] . "'", "publish_date ASC", "");
		$tmpl->bookingTypes = models\bookingTypes::getAll("", "orderby ASC");
		$tmpl->remarkTypes = models\remarkTypes::getAll("");
		$tmpl->clients_th_json = $clientlist;
		$tmpl->accounts_th_json = $accountData;
		$tmpl->sub_placing_th_json = $sub_placingData;
		$tmpl->details_th_json = json_encode($details);;
		$tmpl->marketers = $marketers;
		$tmpl->details = $details;
		$tmpl->placing = $placing;
		$tmpl->inserts_types = $inserts_types;
		$tmpl->accounts = $accounts;
		$tmpl->production = models\production::getAll("pID='$pID' AND ab_production.cID='$cID'", "production ASC");
		$tmpl->categories = models\categories::getAll("pID='$pID' AND ab_categories.cID='$cID'", "orderby ASC");
		$tmpl->dates = array(
			"selected"=>$selectedDate,
			"current"=> $currentDate,
		    "future"=> $dates
		);
		$tmpl->settings  = $settings;



		$tmpl->output();

	}


}
