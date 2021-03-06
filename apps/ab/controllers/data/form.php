<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\ab\controllers\data;

use \timer as timer;
use \apps\ab\models as models;

use \models\user as user;


class form extends data {
	function __construct() {
		parent::__construct();

	}

	function _details($directreturn = false) {
		$timer = new timer();
		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : exit(json_encode(array("error" => $this->f3->get("system")->error("B01"))));
		$settings = models\settings::_read("form");
		$user = $this->f3->get("user");
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];
		$currentDate = $user['publication']['current_date'];


		$cfg = $this->f3->get("CFG");
		$recordO = new models\bookings();
		$record = $recordO->get($ID);
		if ($record['ID'] && ($record['pID'] != $pID)) {
			$record = $recordO->dbStructure();
		}

		$record = json_decode(json_encode($record), true);
		array_walk_recursive($record, "form_display");
		
		
		if (!$user['permissions']['form']['new'] && !$user['permissions']['form']['edit'] && !$user['permissions']['form']['edit_master'] && !$user['permissions']['form']['delete']) {



		}


		$return = array();



		$clientlist = $this->f3->get("DB")->exec("
			SELECT DISTINCT client FROM ab_bookings INNER JOIN global_dates ON ab_bookings.dID = global_dates.ID WHERE ab_bookings.pID = '$pID' AND global_dates.publish_date >= DATE_SUB(now(),INTERVAL '90' DAY) ORDER BY global_dates.publish_date DESC LIMIT 0,200
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

		
		$settings['cID']=$user['company']['ID'];
		$settings['pID']=$user['publication']['ID'];
		
		
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

		//test_array($user);

		$extra_where = "";
		if ($user['permissions']['form']['all_suggestions']!='1'){
			$extra_where = "AND ab_bookings.userID = '". $userID."'";
		}

		$timer = new timer();

		if ($accNum) {

			$where = "WHERE accountID = '$accNum' AND ab_bookings.pID = '$pID' AND ab_bookings.typeID = '$type' AND DATE_SUB(now(),INTERVAL '60' DAY) < publishDate $extra_where ORDER BY publishDate DESC";


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

			$d = models\bookings::getAll_select("ab_bookings.ID, client, global_dates.publish_date as publishDate, totalCost, totalspace, cm, col, InsertPO, ab_bookings.typeID, account as heading, classifiedWords", "accountID = '$accNum' AND ab_bookings.pID = '$pID' AND ab_bookings.typeID = '$type' AND deleted is null $extra_where", "global_dates.publish_date DESC LIMIT 0,6");

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
	function _accounts(){
		$timer = new timer();
		$user = $this->f3->get("user");
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];


		$return = array();
		$search = isset($_REQUEST['search'])?$_REQUEST['search']:"";

		//$search = "cas";

		if ($search){
			$where = "pID='$pID' AND ab_accounts.cID='$cID' AND (ab_accounts.account LIKE :search1 OR ab_accounts.accNum LIKE  :search2 OR ab_accounts.remark LIKE  :search3)";


			$args[':search1'] = '%' . $search . '%';
			$args[':search2'] = '%' . $search . '%';
			$args[':search3'] = '%' . $search . '%';

			$return['records'] = models\accounts::getAll($where, "last_used DESC, account ASC", "", "", array("args" => $args));
		} else {
			$return['records'] = array();
		}

		$return['count'] = count($return['records']);

		$timer->stop("Controller - _accounts", array("search" => $search));
		return $GLOBALS["output"]['data'] = $return;
	}

}
