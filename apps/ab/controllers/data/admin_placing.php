<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\ab\controllers\data;

use \timer as timer;
use \apps\ab\models as models;
use \models\user as user;


class admin_placing extends data {
	function __construct() {
		parent::__construct();

	}

	function _list() {
		$user = $this->f3->get("user");
		$userID = $user['ID'];

		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];





		$where = "pID='$pID'";



		$records = models\placing::getAll($where, "orderby ASC");

		$return = array();

		$return['records'] = $records;

		if (!count($records)) {
			$copyfrom = array();

			$publications = \models\publications::getAll("cID = '" . $user['company']['ID'] . "'");

			if (count($publications)) {
				$pubIDs = array();
				foreach ($publications as $publication) {
					$pubIDs[] = $publication['ID'];
				}


				$records = models\placing::getAll("pID in (" . implode(",", $pubIDs) . ")", "orderby ASC");

				$a = array();
				foreach ($records as $record) {
					$a[$record['pID']] = isset($a[$record['pID']]) ? $a[$record['pID']] + 1 : 1;
				}
				//test_array($a);
				foreach ($publications as $publication) {
					if (isset($a[$publication['ID']]) && ($a[$publication['ID']])) {
						$copyfrom[] = array(
							"ID"    => $publication['ID'],
							"label" => $publication['publication'],
							"count" => isset($a[$publication['ID']]) ? $a[$publication['ID']] : 0
						);
					}

				}


				$return['copyfrom'] = $copyfrom;
			}

		}


		return $GLOBALS["output"]['data'] = $return;
	}

	function _details() {
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];

		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";

		$o = new models\placing();
		$details = $o->get($ID);

		$ID = $details['ID'];


		$return = array();


		$return = array();

		$return['details'] = $details;
		$return['publication'] = $user['publication'];

		$return['sub_placing'] = models\sub_placing::getAll("placingID = '". $details['ID']."'", "orderby ASC");

		if ($details['ID']) {
			$where = "ab_bookings.placingID='" . $details['ID'] . "'";
			$recordsFound = models\bookings::getAll_count($where);
		} else {
			$recordsFound = 0;
		}
		$return['records'] = $recordsFound;


		return $GLOBALS["output"]['data'] = $return;
	}

}
