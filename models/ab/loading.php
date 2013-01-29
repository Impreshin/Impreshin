<?php

namespace models\ab;

use \F3 as F3;
use \Axon as Axon;
use \timer as timer;
use \models\ab\publications as publications;

class loading {
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
			FROM ab_page_load
			WHERE ID = '$ID';

		");


		if (count($result)) {
			$return = $result[0];
		} else {
			$return = $this->dbStructure;
		}
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getAll($where = "", $orderby = "") {
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
			SELECT ab_page_load.*
			FROM ab_page_load
			$where
			$orderby
		");


		$return = $result;
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getLoading($pID = "", $cm = "", $forcepages = "") {
		$timer = new timer();
		$f3 = \Base::instance();


		$user = $f3->get("user");
		$userID = $user['ID'];
		if (!$pID) $pID = $user['pID'];

		if ($pID == $user['pID']) {
			$publication = $user['publication'];
		} else {
			$publication = new publications();
			$publication = $publication->get($pID);
		}

		$return = array(
			"pages"   => 0,
			"loading" => 0,
			"other"   => array(),
			"forced"  => false,
			"error"   => ""
		);


		if ($forcepages) {
			$forcepages = $f3->get("DB")->exec("
				SELECT *, ABS( pages - $forcepages ) AS distance FROM ab_page_load
				WHERE pID = '$pID'
				ORDER BY distance
				LIMIT 6
			");
			$forcepages = $forcepages[0]["pages"];
			$return['forced'] = true;
		}


		$pageSize = $publication['cmav'] * ($publication['columnsav']);


		$loadingData = $f3->get("DB")->exec("
				SELECT * FROM 	ab_page_load WHERE pID = '$pID' ORDER BY pages ASC
			");


		$loading = array();
		$use = "";
		$i = 0;
		foreach ($loadingData as $item) {
			$pages = $item['pages'];
			$percent = $item['percent'];

			if ($pages > 0 && $percent > 0) {
				$avspace = $pages * $pageSize;

				$keepin = $avspace * ($percent / 100);

				$loading[$item['ID']] = array(
					"pages"   => $pages,
					"loading" => number_format(($cm / $avspace) * 100, 2),
					"nr"      => $i
				);

				if ($forcepages) {
					if ($item['pages'] == $forcepages) {
						$use = $item['ID'];

					}
				} else {
					if ($cm <= $keepin && $use == "") {
						$use = $item['ID'];
					}
				}
			}


			$i++;

		}


		//$loading[$use]['current'] = $i;

		if (!$use) {
			if (isset($loadingData[count($loadingData) - 1]['ID'])) {
				$use = $loadingData[count($loadingData) - 1]['ID'];
			} else {
				$use = "";

			}

			$return['error'] = "Please check your loading settings, the current loading trumps your highest page number";
		}

		if ($use) {
			$return['pages'] = $loading[$use]['pages'];
			$return['loading'] = $loading[$use]['loading'];


			$cur = $loading[$use]['nr'];
			$a = array();
			foreach ($loading as $item) {
				$item['current'] = ($item['nr'] == $cur) ? "*" : '';
				unset($item['nr']);
				$a[] = $item;
			}
			$loading = $a;

			if (isset($loading[$cur - 2])) $return['other'][] = $loading[$cur - 2];
			if (isset($loading[$cur - 1])) $return['other'][] = $loading[$cur - 1];
			if (isset($loading[$cur])) $return['other'][] = $loading[$cur];
			if (isset($loading[$cur + 1])) $return['other'][] = $loading[$cur + 1];
			if (isset($loading[$cur + 2])) $return['other'][] = $loading[$cur + 2];
		}


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function save($ID, $values) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");

		$old = array();
		$lookupColumns = array();
		$a = new \DB\SQL\Mapper($f3->get("DB"),"ab_page_load");
		$a->load("ID='$ID'");

		foreach ($values as $key => $value) {
			if (isset($a->$key)) {
				$old[$key] = isset($a->$key) ? $a->$key : "";
				$a->$key = $value;
			}
		}
		if (!$a->dry) {
			$label = "Record Edited ($a->pages [$a->percent%])";
		} else {
			$label = "Record Added (" . $values['pages'] . '[' . $values['percent'] . '%])';
		}
		$a->save();

		$ID = $a->ID;


		//test_array($new_logging);


		\models\logging::_log("loading", $label, $values, $old);

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $ID;

	}

	public static function _delete($ID) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");


		$a = new \DB\SQL\Mapper($f3->get("DB"),"ab_page_load");
		$a->load("ID='$ID'");

		$a->erase();

		$a->save();


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return "done";

	}


	private static function dbStructure() {
		$f3 = \Base::instance();
		$table = $f3->get("DB")->exec("EXPLAIN ab_page_load;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}

		return $result;
	}

}