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


class form extends data {
	function __construct() {
		$this->f3 = \base::instance();
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		if (!$userID) exit(json_encode(array("error" => $this->f3->get("system")->error("U01"))));

	}

	function _details($directreturn = false) {
		$timer = new timer();
		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : exit(json_encode(array("error" => $this->f3->get("system")->error("B01"))));
		$settings = models\settings::_read("form");
		$user = $this->f3->get("user");
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];
		$currentDate = $user['publication']['current_date'];


		$cfg = $this->f3->get("cfg");
		$recordO = new models\bookings();
		$record = $recordO->get($ID);
		if ($record['ID'] && ($record['pID'] != $pID || $record['deleted'] == '1')) {
			$record = $$recordO->dbStructure();
		}


		$return = array();



		$clientlist = $this->f3->get("DB")->exec("
			SELECT DISTINCT client FROM ab_bookings INNER JOIN global_dates ON ab_bookings.dID = global_dates.ID WHERE ab_bookings.pID = '$pID' AND global_dates.publish_date >= DATE_SUB(now(),INTERVAL '160' DAY) ORDER BY global_dates.publish_date DESC LIMIT 0,200
		"
		);
		$a = array();
		foreach ($clientlist AS $client) {
			if ($client['client']) $a[] = $client['client'];
		}
		$clientlist = $a;


		$record['remarkTypeID'] = ($record['remarkTypeID'])? $record['remarkTypeID']:"1";
		if ($record['typeID']) $settings['type'] = $record['typeID'];
		if ($record['marketerID']) $settings['last_marketer'] = $record['marketerID'];
		if ($record['categoryID']) $settings['last_category'] = $record['categoryID'];
		$settings['printOrder'] = $user['publication']['printOrder'];

		$return['details'] = $record;
		$return['settings'] = $settings;

		$return['clients'] =  $clientlist;


		$timer->stop("Controller - _details", array("ID"     => $record['ID'],"client" => $record['client'] ));
		if ($directreturn) {
			return $return;
		}
		return $GLOBALS["output"]['data'] = $return;
	}

	function account_lookup_history_suggestions() {
		$accNum = (isset($_REQUEST['accNum'])) ? $_REQUEST['accNum'] : "";
		$limit = (isset($_REQUEST['limit'])) ? "LIMIT " . $_REQUEST['limit'] : "";
		$type = (isset($_REQUEST['type'])) ? $_REQUEST['type'] : "1";
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];

		$timer = new timer();

		if ($accNum) {

			$where = "WHERE accountID = '$accNum' AND ab_bookings.pID = '$pID' AND ab_bookings.typeID = '$type' AND DATE_SUB(now(),INTERVAL '60' DAY) < publishDate ORDER BY publishDate DESC";


			$data = $this->f3->get("DB")->exec("
				SELECT
					ab_categories.ID AS categoryID, ab_categories.category,
					ab_marketers.ID AS marketerID, ab_marketers.marketer,
					ab_placing.ID AS placingID, ab_placing.placing,
					discount,agencyDiscount

					FROM ((ab_bookings LEFT JOIN ab_categories ON ab_bookings.categoryID = ab_categories.ID) LEFT JOIN ab_marketers ON ab_bookings.marketerID = ab_marketers.ID) LEFT JOIN ab_placing ON ab_bookings.placingID = ab_placing.ID



				$where

			"
			);


			$d = array();

			$columns = array(
				"marketer"       => "marketerID",
				"category"       => "categoryID",
				"discount"       => "discount",
				"agencyDiscount" => "agencyDiscount",
			);

			if ($type == '1') {
				$columns['placing'] = "placingID";
			}


			foreach ($data as $record) {
				foreach ($columns as $col => $colv) {
					if (!isset($d[$col])) $d[$col] = array();
					if (!isset($d[$col][$record[$colv]])) {
						$d[$col][$record[$colv]] = array(
							"h" => "",
							"v" => "",
							"c" => 0
						);
					}
					$d[$col][$record[$colv]]['v'] = $record[$colv];
					$d[$col][$record[$colv]]['h'] = $record[$col];
					$d[$col][$record[$colv]]['c'] = $d[$col][$record[$colv]]['c'] + 1;

				}


			}


			$data = array();
			foreach ($d as $key => $value) {
				$data[$key] = array();
				foreach ($value as $r) {
					$data[$key][] = $r;
				}


				//usort($data[$key], sortBy('c'));
				$data[$key] = array_slice(array_reverse(($data[$key])), 0, 4);


			}

			$d = models\bookings::getAll_select("ab_bookings.ID, client, global_dates.publish_date as publishDate, totalCost, totalspace, cm, col, InsertPO, typeID, account as heading", "accountID = '$accNum' AND ab_bookings.pID = '$pID' AND ab_bookings.typeID = '$type' AND deleted is null", "global_dates.publish_date DESC LIMIT 0,6");

			$d = models\bookings::display($d);

			if (isset($d[0])) {
				$d = $d[0];
			}
			if (isset($d['records'])) {
				$d = $d['records'];
			} else {
				$d = array();
			}

			$data['history'] = $d;


			$return = $data;
		} else {
			$accounts = $this->f3->get("DB")->exec("
				SELECT ab_accounts.*
				FROM ab_bookings INNER JOIN ab_accounts ON ab_bookings.accountID = ab_accounts.ID
				WHERE ab_bookings.userID = '$userID' AND pID = '$pID'
				ORDER BY datein DESC
				LIMIT 0,50
			"
			);


			$limit = (isset($_REQUEST['limit'])) ? $_REQUEST['limit'] : "";

			$a = array();
			$aa = array();
			$i = 0;
			foreach ($accounts as $account) {
				if (!in_array($account['ID'], $aa)) {
					$a[] = $account;
				}
				$aa[] = $account['ID'];
				if ($limit && $i++ == $limit + 1) {
					break;
				}

			}
			$accounts = $a;
			$return = array(
				'accounts' => $accounts
			);
		}

		$timer->stop("Controller - account_lookup_history_suggestions", array(
			                                                              "accNum" => $accNum,
			                                                              "pID"    => $pID
		                                                              )
		);
		return $GLOBALS["output"]['data'] = $return;
	}

}
