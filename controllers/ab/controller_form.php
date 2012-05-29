<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace controllers\ab;
use \F3 as F3;
use \timer as timer;
use models\ab as models;
use models\ab\bookings as bookings;
use models\ab\accounts as accounts;
use models\ab\marketers as marketers;
use models\ab\categories as categories;
use models\ab\placing as placing;
use models\ab\colours as colours;
use models\ab\dates as dates;
use models\ab\bookingTypes as bookingTypes;
use models\ab\remarkTypes as remarkTypes;
use models\ab\production as production;
use models\ab\publications as publications;

class controller_form {
	function __construct() {
		$user = F3::get("user");
		$userID = $user['ID'];
		if (!$userID) F3::reroute("/login");
		\models\user::save_config(array("page"=> $_SERVER['REQUEST_URI']));
	}

	function page() {
		$ID = F3::get('PARAMS["ID"]');
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['ab_pID'];

		$currentDate = dates::getCurrent($pID);
		$dID = $currentDate['ID'];




		$clientlist = F3::get("DB")->exec("
			SELECT Distinct client FROM ab_bookings INNER JOIN global_dates ON ab_bookings.dID = global_dates.ID WHERE ab_bookings.pID = '$pID' AND global_dates.publish_date >= DATE_SUB(now(),INTERVAL '60' DAY) ORDER BY global_dates.publish_date DESC LIMIT 0,200
		"
		);
		$a = array();
		foreach ($clientlist AS $client) {
			$a[] = array(
				"label"=> $client['client']
			);
		}

		$clientlist = json_encode($a);

		$spotlist = F3::get("DB")->exec("
			SELECT Distinct colourSpot FROM ab_bookings WHERE pID='$pID'
		"
		);
		$a = array();
		foreach ($spotlist AS $record) {
			$a[] = array(
				"label"=> $record['colourSpot']
			);
		}
		$spotlist = json_encode($a);

		$accounts = accounts::getAll("pID='$pID'", "account ASC");
		$marketers = marketers::getAll("pID='$pID'", "marketer ASC");
		$dates = dates::getAll("pID='$pID' AND publish_date > '".$currentDate['publish_date']."'", "publish_date ASC", "");
		$placing = placing::getAll("pID='$pID'", "orderby ASC", "");
		$colours = colours::getAll("pID='$pID'", "orderby ASC", "");

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

		$details = new bookings();
		$details = $details->get($ID);



		$tmpl = new \template("template.tmpl", "ui/adbooker/");
		$tmpl->page = array(
			"section"    => "form",
			"sub_section"=> "form",
			"template"   => "page_form",
			"meta"       => array(
				"title"=> "AdBooker - Form",
			)
		);


		$tmpl->bookingTypes = bookingTypes::getAll("", "orderby ASC");
		$tmpl->remarkTypes = remarkTypes::getAll("");
		$tmpl->clients_th_json = $clientlist;
		$tmpl->spots_th_json = $spotlist;
		$tmpl->accounts_th_json = $accountData;
		$tmpl->colours_th_json = $colourData;
		$tmpl->details_th_json = json_encode($details);;
		$tmpl->marketers = $marketers;
		$tmpl->details = $details;
		$tmpl->placing = $placing;
		$tmpl->production = production::getAll("pID='$pID'", "production ASC");
		$tmpl->categories = categories::getAll("pID='$pID'", "orderby ASC");
		$tmpl->dates = array(
			"current"=> $currentDate,
		    "future"=> $dates
		);

		$tmpl->output();

	}


}
