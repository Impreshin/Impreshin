<?php

namespace models\ab;
use \F3 as F3;
use \Axon as Axon;
use \timer as timer;
class accounts {
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
			SELECT ab_accounts.*
			FROM ab_accounts
			WHERE ab_accounts.ID = '$ID';

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

	public static function getAll_count($where = "", $orderby = "") {
		$timer = new timer();
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}

		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}


		$result = F3::get("DB")->exec("
			SELECT count(DISTINCT ab_accounts.ID) as count
			FROM (ab_accounts INNER JOIN ab_accounts_status ON ab_accounts.statusID = ab_accounts_status.ID) LEFT JOIN ab_accounts_pub ON ab_accounts.ID = ab_accounts_pub.aID
			$where
			$orderby
		"
		);


		if (count($result)) {
			$return = $result[0]['count'];

		} else {
			$return = 0;
		}
		$timer->stop(array("Models"=> array("Class" => __CLASS__,
		                                    "Method"=> __FUNCTION__
  )
		             ), func_get_args()
		);
		return $return;
	}

	public static function getAll($where = "", $orderby = "", $limit = "") {
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

		if ($limit) {
			$limit = str_replace("LIMIT", "", $limit);
			$limit = " LIMIT " . $limit;

		}




		$result = F3::get("DB")->exec("
			SELECT DISTINCT ab_accounts.*, ab_accounts_status.blocked, ab_accounts_status.labelClass, ab_accounts_status.status, ab_accounts.ID as ID, if ((SELECT count(ID) FROM ab_accounts_pub WHERE ab_accounts_pub.aID = ab_accounts.ID AND ab_accounts_pub.pID = '$pID' LIMIT 0,1)<>0,1,0) as currentPub

			FROM ((ab_accounts INNER JOIN ab_accounts_status ON ab_accounts.statusID = ab_accounts_status.ID) LEFT JOIN ab_accounts_pub ON ab_accounts.ID = ab_accounts_pub.aID) LEFT JOIN global_publications ON ab_accounts_pub.pID = global_publications.ID
			$where
			$orderby
			$limit
		");


		$return = $result;
		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function save($ID, $values) {
		$user = F3::get("user");
		$timer = new timer();



		$a = new Axon("ab_accounts");
		$a->load("ID='$ID'");

		foreach ($values as $key=> $value) {
			$a->$key = $value;
		}

		$a->save();

		if (!$a->ID) {
			$ID = $a->_id;
		}

		$cID = $values['cID'];
		if (!$cID){
			$cID=$user['publication']['cID'];
		}

		$p = new Axon("ab_accounts_pub");
		$publications = publications::getAll("cID='$cID'", "publication ASC");

		foreach ($publications as $publication) {
			$p->load("pID='" . $publication['ID'] . "' AND aID='" . $ID . "'");
			if (in_array($publication['ID'], $values['publications'])) {
				$p->pID = $publication['ID'];
				$p->aID = $ID;
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

		$a = new Axon("ab_accounts");
		$a->load("ID='$ID'");

		$a->erase();

		$a->save();




		$timer->stop(array("Models"=> array("Class" => __CLASS__,"Method"=> __FUNCTION__)), func_get_args());
		return "done";

	}


	private static function dbStructure() {
		$table = F3::get("DB")->exec("EXPLAIN ab_accounts;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}

		return $result;
	}
}