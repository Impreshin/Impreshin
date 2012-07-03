<?php

namespace models\ab;
use \F3 as F3;
use \Axon as Axon;
use \timer as timer;
class marketers {
	private $classname;
	function __construct() {

		$classname = get_class($this);
		$this->dbStructure = $classname::dbStructure();

	}
	function get($ID){
		$timer = new timer();
		$user = F3::get("user");
		$userID = $user['ID'];


		$result = F3::get("DB")->exec("
			SELECT ab_marketers.*
			FROM ab_marketers
			WHERE ab_marketers.ID = '$ID';

		"
		);


		if (count($result)) {
			$return = $result[0];
		} else {
			$return = $this->dbStructure;
		}
		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}
	public static function getAll($where = "", $orderby = "") {
		$timer = new timer();
		$user = F3::get("user");
		$pID = $user['publication']['ID'];
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}

		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}

		$month = date("m");
		$year = date("Y");

		$target = ", (SELECT target FROM ab_marketers_targets WHERE ab_marketers_targets.mID = ab_marketers.ID AND ab_marketers_targets.pID ='$pID' AND monthin='$month' AND yearin='$year' ORDER BY ab_marketers_targets.ID DESC LIMIT 0,1) as currentTarget";



		$result = F3::get("DB")->exec("
			SELECT DISTINCT ab_marketers.*, if ((SELECT count(ID) FROM ab_marketers_pub WHERE ab_marketers_pub.mID = ab_marketers.ID AND ab_marketers_pub.pID = '$pID' LIMIT 0,1)<>0,1,0) as currentPub
			$target
			FROM ab_marketers LEFT JOIN ab_marketers_pub ON ab_marketers.ID = ab_marketers_pub.mID
			$where
			$orderby
		");
		$a = array();
		foreach ($result as $record){
			if ($record['currentTarget']) $record['currentTarget'] = currency($record['currentTarget']);
			$a[] = $record;
		}



		$return = $a;
		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}
	public static function save($ID, $values) {
		$user = F3::get("user");
		$timer = new timer();

		$a = new Axon("ab_marketers");
		$a->load("ID='$ID'");

		foreach ($values as $key=> $value) {
			$a->$key = $value;
		}

		$a->save();

		if (!$a->ID) {
			$ID = $a->_id;
		}

		$cID = $values['cID'];
		if (!$cID) {
			$cID = $user['publication']['cID'];
		}

		$p = new Axon("ab_marketers_pub");
		$publications = publications::getAll("cID='$cID'", "publication ASC");

		foreach ($publications as $publication) {
			$p->load("pID='" . $publication['ID'] . "' AND mID='" . $ID . "'");
			if (in_array($publication['ID'], $values['publications'])) {
				$p->pID = $publication['ID'];
				$p->mID = $ID;
				$p->save();
			} else {
				if (!$p->dry()) {
					$p->erase();
				}
			}
			$p->reset();
		}



		$timer->stop(array("Models"=> array("Class" => __CLASS__,"Method"=> __FUNCTION__)), func_get_args());
		return $ID;

	}

	public static function _delete($ID) {
		$user = F3::get("user");
		$timer = new timer();

		$a = new Axon("ab_marketers");
		$a->load("ID='$ID'");

		$a->erase();

		$a->save();




		$timer->stop(array("Models"=> array("Class" => __CLASS__,"Method"=> __FUNCTION__)), func_get_args());
		return "done";

	}



	private static function dbStructure() {
		$table = F3::get("DB")->exec("EXPLAIN ab_marketers;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}

		return $result;
	}
}