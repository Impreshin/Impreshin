<?php

namespace apps\ab\models;



use \timer as timer;

class production {
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
			SELECT *
			FROM ab_production
			WHERE ab_production.ID = '$ID';

		");


		if (count($result)) {
			$return = $result[0];
		} else {
			$return = $this->dbStructure;
		}
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getAll($where = "", $orderby = "",$pID="") {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		$pID = $pID? $pID:  $user['publication']['ID'];
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}

		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}


		$result = $f3->get("DB")->exec("
			SELECT DISTINCT ab_production.*, if ((SELECT count(ID) FROM ab_production_pub WHERE ab_production_pub.productionID = ab_production.ID AND ab_production_pub.pID = '$pID' LIMIT 0,1)<>0,1,0) AS currentPub
			FROM ab_production LEFT JOIN ab_production_pub ON ab_production.ID = ab_production_pub.productionID
			$where
			$orderby
		");


		$return = $result;
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function save($ID, $values) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");

		$old = array();
		$lookupColumns = array();

		//test_array($values);
		$a = new \DB\SQL\Mapper($f3->get("DB"),"ab_production");
		$a->load("ID='$ID'");

		foreach ($values as $key => $value) {
			$old[$key] = isset($a->$key) ? $a->$key : "";
			if (isset($a->$key)) {
				$a->$key = $value;
			}
		}

		if (!$a->dry()) {
			$label = "Record Edited ($a->production)";
		} else {
			$label = "Record Added (" . $values['production'] . ')';
		}
		$a->save();
		//test_array($values);
		$ID = $a->ID;

		$cID = $values['cID'];
		if (!$cID) {
			$cID = $user['company']['ID'];
		}

		$p = new \DB\SQL\Mapper($f3->get("DB"),"ab_production_pub");
		$publications = \models\publications::getAll("cID='$cID'", "publication ASC");
		$pub = array(
			"a" => array(),
			"r" => array()
		);

		foreach ($publications as $publication) {
			$p->load("pID='" . $publication['ID'] . "' AND productionID='" . $ID . "'");
			if (in_array($publication['ID'], $values['publications'])) {
				$p->pID = $publication['ID'];
				$p->productionID = $ID;
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





		\models\logging::_log("production", $label, $values, $old, $overwrite, $lookupColumns);

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $ID;

	}

	public static function _delete($ID) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");


		$a = new \DB\SQL\Mapper($f3->get("DB"),"ab_production");
		$a->load("ID='$ID'");

		$a->erase();

		$a->save();


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return "done";

	}

	private static function dbStructure() {
		$f3 = \Base::instance();
		$table = $f3->get("DB")->exec("EXPLAIN ab_production;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}

		return $result;
	}
}