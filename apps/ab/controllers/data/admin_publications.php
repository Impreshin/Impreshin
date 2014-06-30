<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\ab\controllers\data;

use models\global_colours;
use \timer as timer;
use \apps\ab\models as models;
use \models\user as user;


class admin_publications extends data {
	function __construct() {
		parent::__construct();

	}

	function _list() {
		$user = $this->f3->get("user");
		$userID = $user['ID'];

		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];





		$where = "cID='$cID'";



		$records = \models\publications::getAll($where, "publication ASC");

		$return = array();

		$return['records'] = $records;

		return $GLOBALS["output"]['data'] = $return;
	}

	function _details() {
		$user = $this->f3->get("user");
		$cfg = $this->f3->get("CFG");
		$userID = $user['ID'];
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];

		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";

		$o = new \models\publications();
		$details = $o->get($ID);

		$ID = $details['ID'];

		if ($details['cmav']) $details['cmav'] = $details['cmav'] + 0;
		if ($details['pagewidth']) $details['pagewidth'] = $details['pagewidth'] + 0;

		$used = array();
		foreach ($details['colours'] as $colour){
			$used[] = $colour['ID'];
		}

		$colours = \models\global_colours::getAll();
		$n = array();
		foreach ($colours as $colour) {
			$colour['selected'] = '0';
			$colour['locked'] = '0';
			if (in_array($colour['ID'],$used))	$colour['selected'] = '1';
			if (in_array($colour['ID'], $cfg['default_colours'])) $colour['locked'] = '1';

			$n[] = $colour;
		}
		$colours = $n;


		$details['colours'] = $colours;

		$return = array();

		$return['details'] = $details;

		if ($details['ID']) {
			$where = "ab_bookings.pID='" . $details['ID'] . "'";
			$recordsFound = models\bookings::getAll_count($where);
		} else {
			$recordsFound = 0;
		}
		$return['records'] = $recordsFound;

		return $GLOBALS["output"]['data'] = $return;
	}

}
