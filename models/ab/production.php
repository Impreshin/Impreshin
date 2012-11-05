<?php

namespace models\ab;
use \F3 as F3;
use \Axon as Axon;
use \timer as timer;
class production {
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
			SELECT *
			FROM ab_production
			WHERE ab_production.ID = '$ID';

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




		$result = F3::get("DB")->exec("
			SELECT DISTINCT ab_production.*, if ((SELECT count(ID) FROM ab_production_pub WHERE ab_production_pub.productionID = ab_production.ID AND ab_production_pub.pID = '$pID' LIMIT 0,1)<>0,1,0) as currentPub
			FROM ab_production LEFT JOIN ab_production_pub ON ab_production.ID = ab_production_pub.productionID
			$where
			$orderby
		");


		$return = $result;
		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function save($ID, $values) {
		$user = F3::get("user");
		$timer = new timer();
		$old = array();
		$lookupColumns = array();

		$a = new Axon("ab_production");
		$a->load("ID='$ID'");

		foreach ($values as $key=> $value) {
			$old[$key] = $a->$key;
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

		$p = new Axon("ab_production_pub");
		$publications = publications::getAll("cID='$cID'", "publication ASC");
		$pub = array(
			"a"=> array(),
			"r"=> array()
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
				"k"=> "publications",
				"v"=> implode(" | ", $str),
				"w"=> '-'
			);
			$overwrite['publications'] = $pub;
		}


		//test_array($changes);

		if ($a->ID) {
			$label = "Record Edited ($a->production)";
		} else {
			$label = "Record Added (" . $values['production'] . ')';
		}
		//test_array($new_logging);


		\models\logging::_log("production", $label, $values, $old, $overwrite, $lookupColumns);

		$timer->stop(array("Models"=> array("Class" => __CLASS__,"Method"=> __FUNCTION__)), func_get_args());
		return $ID;

	}

	public static function _delete($ID) {
		$user = F3::get("user");
		$timer = new timer();

		$a = new Axon("ab_production");
		$a->load("ID='$ID'");

		$a->erase();

		$a->save();




		$timer->stop(array("Models"=> array("Class" => __CLASS__,"Method"=> __FUNCTION__)), func_get_args());
		return "done";

	}

	private static function dbStructure() {
		$table = F3::get("DB")->exec("EXPLAIN ab_production;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}

		return $result;
	}
}