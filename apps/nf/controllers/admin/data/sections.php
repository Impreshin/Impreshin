<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\nf\controllers\admin\data;

use \timer as timer;
use \apps\nf\models as models;
use \models\user as user;


class sections extends \apps\nf\controllers\data\data {
	function __construct() {
		parent::__construct();

	}

	function _list() {
		$user = $this->f3->get("user");
		$userID = $user['ID'];

		$pID = $user['publication']['ID'];





		$where = "pID='$pID'";



		$records = models\sections::getAll($where, "section ASC");

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


				$records = models\sections::getAll("pID in (" . implode(",", $pubIDs) . ")", "section ASC");

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

		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";

		$o = new models\sections();
		$details = $o->get($ID);



		$return = array();


		$return['details'] = $details;




		return $GLOBALS["output"]['data'] = $return;
	}

}
