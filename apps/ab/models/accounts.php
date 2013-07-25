<?php

namespace apps\ab\models;
use \timer as timer;

class accounts {
	private $classname;

	function __construct() {

		$classname = get_class($this);
		$this->dbStructure = $classname::dbStructure();

	}

	function get($ID) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		$userID = $user['ID'];


		$result = $f3->get("DB")->exec("
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
		$timer->stop(array("Models" => array("Class"  => __CLASS__,
		                                     "Method" => __FUNCTION__
  )
		             ), func_get_args()
		);
		return $return;
	}

	public static function getAll_count($where = "", $orderby = "") {
		$timer = new timer();
		$f3 = \Base::instance();
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}

		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}


		$result = $f3->get("DB")->exec("
			SELECT count(DISTINCT ab_accounts.ID) AS count
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
		$timer->stop(array(
			             "Models" => array(
				             "Class"  => __CLASS__,
				             "Method" => __FUNCTION__
			             )
		             ), func_get_args()
		);
		return $return;
	}

	public static function getAll($where = "", $orderby = "", $limit = "", $pID="", $options=array()) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		$pID = $pID ? $pID : $user['publication']['ID'];
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
		$ttl = "";
		$args = "";
		if (isset($options['ttl'])) $ttl = $options['ttl'];
		if (isset($options['args'])) $args = $options['args'];



		$result = $f3->get("DB")->exec("
			SELECT DISTINCT ab_accounts.*, ab_accounts_status.blocked, ab_accounts_status.labelClass, ab_accounts_status.status, ab_accounts.ID AS ID, if ((SELECT count(ID) FROM ab_accounts_pub WHERE ab_accounts_pub.aID = ab_accounts.ID AND ab_accounts_pub.pID = '$pID' LIMIT 0,1)<>0,1,0) AS currentPub,
			(SELECT global_dates.publish_date FROM ab_bookings INNER JOIN global_dates ON ab_bookings.dID = global_dates.ID WHERE ab_bookings.accountID = ab_accounts.ID ORDER BY global_dates.publish_date DESC LIMIT 0,1) AS last_used

			FROM ((ab_accounts INNER JOIN ab_accounts_status ON ab_accounts.statusID = ab_accounts_status.ID) LEFT JOIN ab_accounts_pub ON ab_accounts.ID = ab_accounts_pub.aID) LEFT JOIN global_publications ON ab_accounts_pub.pID = global_publications.ID
			$where
			$orderby
			$limit
		", $args
		);


		$return = $result;
		$timer->stop(array("Models" => array("Class"  => __CLASS__,
		                                     "Method" => __FUNCTION__
  )
		             ), func_get_args()
		);
		return $return;
	}

	public static function save($ID, $values) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");


		if (isset($values['ID'])) unset($values['ID']);
		$old = array();
		$lookupColumns = array();
		$lookupColumns["statusID"] = array(
			"sql" => "(SELECT status FROM ab_accounts_status WHERE ID = '{val}')",
			"col" => "status",
			"val" => ""
		);

		$a = new \DB\SQL\Mapper($f3->get("DB"),"ab_accounts");
		$a->load("ID='$ID'");


		foreach ($values as $key => $value) {
			if ($key!="ID"){
				$old[$key] = isset($a->$key) ? $a->$key : "";
				if (isset($a->$key)) {

					$a->$key = $value;
				}
			}

		}

		$a->save();
		$ID = $a->ID;

		$cID = $values['cID'];
		if (!$cID) {
			$cID = $user['publication']['cID'];
		}

		$p = new \DB\SQL\Mapper($f3->get("DB"),"ab_accounts_pub");
		$publications = \models\publications::getAll("cID='$cID'", "publication ASC");


		$pub = array(
			"a" => array(),
			"r" => array()
		);
		foreach ($publications as $publication) {
			$pID = $publication['ID'];
			$p->load("pID='" . $pID . "' AND aID='" . $ID . "'");
			if (in_array($pID, $values['publications'])) {
				$p->pID = $pID;
				$p->aID = $ID;


				if (!$p->ID) {
					$pub['a'][] = $publication['publication'];
					$p->save();
				}

			} else {
				if ($p->ID) {
					$pub['r'][] = $publication['publication'];
					$p->erase();
				}
			}
			$p->reset();

		}


		$str = array();
		if (count($pub['a'])) $str[] = "Added: " . implode(", ", $pub['a']);
		if (count($pub['r'])) $str[] = "Removed: " . implode(", ", $pub['r']);

		$overwrite = array("publications");
		if (count($str)) {
			$pub = array(
				"k" => "publications",
				"v" => implode(" | ", $str),
				"w" => '-'
			);
			$overwrite['publications'] = $pub;
		}


		//test_array($changes);

		if (!$a->dry()) {
			$label = "Record Edited ($a->account)";
		} else {
			$label = "Record Added (" . $values['account'] . ')';
		}
		//test_array($new_logging);


		//\models\logging::_log("accounts", $label, $values, $old, $overwrite, $lookupColumns);


		$timer->stop(array("Models" => array("Class"  => __CLASS__,
		                                     "Method" => __FUNCTION__
  )
		             ), func_get_args()
		);
		return $ID;

	}


	public static function _delete($ID) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");


		$a = new \DB\SQL\Mapper($f3->get("DB"),"ab_accounts");
		$a->load("ID='$ID'");

		$a->erase();

		$a->save();


		$timer->stop(array("Models" => array("Class"  => __CLASS__,
		                                     "Method" => __FUNCTION__
  )
		             ), func_get_args()
		);
		return "done";

	}


	private static function dbStructure() {
		$f3 = \Base::instance();
		$table = $f3->get("DB")->exec("EXPLAIN ab_accounts;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}

		return $result;
	}
}