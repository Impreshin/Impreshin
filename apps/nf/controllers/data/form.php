<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\nf\controllers\data;

use \timer as timer;
use \apps\nf\models as models;

use \models\user as user;


class form extends data {
	function __construct() {
		parent::__construct();

	}

	function _details($directreturn = false) {
		$timer = new timer();
		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";
		$settings = models\settings::_read("form");
		$user = $this->f3->get("user");
		$cID = $user['company']['ID'];



		$cfg = $this->f3->get("CFG");
		$recordO = new models\articles();
		$record = $recordO->get($ID);

		if ($record['ID'] && ($record['cID'] != $cID)) {
			$record = $recordO->dbStructure();
		}

		



		//test_array($record);



		$return = array();


		if ($record['authorID']) $settings['last_author'] = $record['authorID'];
		if ($record['categoryID']) $settings['last_category'] = $record['categoryID'];
		if ($record['typeID']) {
			$settings['last_type'] = $record['typeID'];
		} else {
			
		}


		$return['details'] = $record;
		$return['settings'] = $settings;
		$return['stageNext'] = models\stages::getNext($record['stageID']);
		$permissions = $user['permissions'];
		
		if (isset($permissions['stages'][$return['stageNext']['ID']])){
			if ($permissions['stages'][$return['stageNext']['ID']]['to']!='1'){
				$return['stageNext'] = array();
			}
		} else {
			$return['stageNext'] = array();
		} 
		
		$stage_permissions = isset($permissions['stages'][$record['stageID']])?$permissions['stages'][$record['stageID']]:array();

		$allow = array(
			'delete'=>'0'
		);
		if (isset($stage_permissions['delete']) && $stage_permissions['delete']=='1'){
			$allow['delete']='1';
		}
		$return['a'] = $allow;
		$timer->stop("Controller - _details", array("ID" => $record['ID'], "title" => $record['title']));
		if ($directreturn) {
			return $return;
		}

		return $GLOBALS["output"]['data'] = $return;
	}

	function checklists($directreturn = false) {
		$timer = new timer();
		$ID = (isset($_REQUEST['categoryID'])) ? $_REQUEST['categoryID'] : "";
		$selected = (isset($_REQUEST['selected'])) ? $_REQUEST['selected'] : "";





		$return = models\checklists::getAll("categoryID='" . $ID . "'", "orderby ASC");
		if (isset($_REQUEST['selected'])) {
			$selected = explode(",", $selected);

			$t = array();
			foreach ($return as $item) {
				$item['selected'] = in_array($item['ID'], $selected) ? "1" : "0";
				$t[] = $item;
			}

			$return = $t;
		}


		$timer->stop("Controller - _checklists", array("categoryID" => $ID));
		if ($directreturn) {
			return $return;
		}

		return $GLOBALS["output"]['data'] = $return;
	}



}
