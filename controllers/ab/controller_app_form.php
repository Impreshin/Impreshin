<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace controllers\ab;
use \F3 as F3;
use \timer as timer;
use models\ab as models;

class controller_app_form {
	function __construct() {
	}

	function page() {
		$ID = F3::get('PARAMS["ID"]');
		$user = F3::get("user");
		if (!$user['permissions']['form']['new']&& !$user['permissions']['form']['edit']&&!$user['permissions']['form']['edit_master']&& !$user['permissions']['form']['delete']) F3::error(404);
		$userID = $user['ID'];
		$pID = $user['publication']['ID'];
		$cID = $user['publication']['cID'];

		$currentDate = $user['publication']['current_date'];
		$dID = $currentDate['ID'];

		$settings = models\settings::_read("form");




		$clientlist = F3::get("DB")->exec("
			SELECT Distinct client FROM ab_bookings INNER JOIN global_dates ON ab_bookings.dID = global_dates.ID WHERE ab_bookings.pID = '$pID' AND global_dates.publish_date >= DATE_SUB(now(),INTERVAL '60' DAY) ORDER BY global_dates.publish_date DESC LIMIT 0,200
		"
		);
		$a = array();
		foreach ($clientlist AS $client) {
			if ($client['client']) $a[] = $client['client'];
		}

		$clientlist = json_encode($a);

		$spotlist = F3::get("DB")->exec("
			SELECT Distinct colourSpot FROM ab_bookings WHERE pID='$pID'
		"
		);
		$a = array();
		foreach ($spotlist AS $record) {
			if ($record['colourSpot']) $a[] = $record['colourSpot'];
		}
		$spotlist = json_encode($a);

		$accounts = models\accounts::getAll("pID='$pID' AND ab_accounts.cID='$cID'", "account ASC");
		$marketers = models\marketers::getAll("pID='$pID' AND ab_marketers.cID='$cID'", "marketer ASC");
		$dates = models\dates::getAll("pID='$pID' AND publish_date > '".$currentDate['publish_date']."'", "publish_date ASC", "");
		$placing = models\placing::getAll("pID='$pID'", "orderby ASC", "");
		$inserts_types = models\inserts_types::getAll("pID='$pID'", "orderby ASC", "");
		$colours = models\colours::getAll("pID='$pID'", "orderby ASC", "");

		$c = array();
		foreach ($colours as $record) {
			if (!isset($c[$record['placingID']])) $c[$record['placingID']]['place'] = "None";
			$c[$record['placingID']]['records'][] = $record;
		}

		foreach ($placing AS $record) {
			if (isset($c[$record['ID']])){
				$c[$record['ID']]['place'] = $record['placing'];
			}
		}

		$colourData = json_encode($c);


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
			if (!$user['permissions']['form']['edit'] && !$user['permissions']['form']['edit_master'] && !$user['permissions']['form']['delete']) F3::error(404);
			if ($details['state'] == 'Current' || $details['state'] == 'Future') {
			} else {
				if (!$user['permissions']['form']['edit_master']){
					F3::error(404);
				}

			}

			$title = "".$details['client'];
		} else {
			if (!$user['permissions']['form']['new']) F3::error(404);
		}

		$tmpl = new \template("template.tmpl", "ui/ab/");
		$tmpl->page = array(
			"section"    => "form",
			"sub_section"=> "form",
			"template"   => "page_app_form",
			"meta"       => array(
				"title"=> "AdBooker - Form - $title",
			),
			"help"=>"/ab/help/form"
		);


		$selectedDate = new models\dates();
		$selectedDate = $selectedDate->get($details['dID']);

		$d = array();
		foreach ($dates as $date){
			$d[] = $date['ID'];
		}

		if ($selectedDate['ID'] == $currentDate['ID'] || in_array($selectedDate['ID'],$d) ){
			$selectedDate = array();
		}

		$tmpl->repeat_dates = models\dates::getAll("pID='$pID' AND publish_date >= '" . $currentDate['publish_date'] . "'", "publish_date ASC", "");
		$tmpl->bookingTypes = models\bookingTypes::getAll("", "orderby ASC");
		$tmpl->remarkTypes = models\remarkTypes::getAll("");
		$tmpl->clients_th_json = $clientlist;
		$tmpl->spots_th_json = $spotlist;
		$tmpl->accounts_th_json = $accountData;
		$tmpl->colours_th_json = $colourData;
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
