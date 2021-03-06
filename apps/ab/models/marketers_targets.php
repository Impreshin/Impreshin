<?php

namespace apps\ab\models;



use \timer as timer;

class marketers_targets {
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
			FROM ab_marketers_targets
			WHERE ID = '$ID';

		");


		if (count($result)) {
			$return = $result[0];
		} else {
			$return = $this->dbStructure;
		}
		$timer->stop(array(
		                  "Models" => array(
			                  "Class"  => __CLASS__,
			                  "Method" => __FUNCTION__
		                  )
		             ), func_get_args());
		return $return;
	}


	public static function _current($mID, $pID) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		$result = array();


		$marketer = new marketers();
		$return = $marketer->get($mID);
		
		if ($return['ID']) {

			

			$result = $f3->get("DB")->exec("
				SELECT ab_marketers_targets.*, DATEDIFF(current_date(),date_to)
				FROM ab_marketers_targets INNER JOIN ab_marketers_targets_pub ON ab_marketers_targets.ID = ab_marketers_targets_pub.mtID

				WHERE ab_marketers_targets.mID = '" . $return['ID'] . "' AND ab_marketers_targets_pub.pID ='$pID' AND DATEDIFF(date_to,current_date()) >= 0 AND date_from <= current_date()
				ORDER BY DATEDIFF(date_to,current_date()) ASC
			");

			$done = 0;

		

			if (count($result)) {
				$next = $result[0];

				$start_date = "";
				$end_date = false;
				$targets = array();
				$pubs_array = array();
				foreach ($result as $target) {
					$pubs = $f3->get("DB")->exec("SELECT pID FROM ab_marketers_targets_pub WHERE mtID = '" . $target['ID'] . "'");

					$p = array();
					foreach ($pubs as $pub) {


						if (!in_array($pub['pID'], $pubs_array)) {
							$pubs_array[] = $pub['pID'];
						}
						$p[] = $pub['pID'];
					}
					$pubs = $p;
					if ($start_date) {
						if ($target['date_from'] < $start_date) $start_date = date("Y-m-d", strtotime($target['date_from']));
					} else {
						$start_date = date("Y-m-d", strtotime($target['date_from']));
					}
					if ($end_date) {
						if ($target['date_to'] > $end_date) $end_date = date("Y-m-d", strtotime($target['date_to']));
					} else {
						$end_date = date("Y-m-d", strtotime($target['date_to']));
					}


					$r = array();
					$r['date_from'] = date("Y-m-d", strtotime($target['date_from']));
					$r['date_to'] = date("Y-m-d", strtotime($target['date_to']));
					$r['target'] = $target['target'];
					$r['done'] = 0;
					$r['records'] = 0;
					$r['percent'] = 0;
					$r['pubs'] = $pubs;
					$targets[$target['ID']] = $r;


				}


				$pubs = implode(",", $pubs_array);


				$b = $f3->get("DB")->exec("SELECT ab_bookings.*, global_dates.publish_date as publishDate FROM ((((ab_bookings LEFT JOIN global_users ON ab_bookings.userID = global_users.ID) LEFT JOIN global_publications ON ab_bookings.pID = global_publications.ID) LEFT JOIN global_dates ON ab_bookings.dID = global_dates.ID) INNER JOIN ab_remark_types ON ab_bookings.remarkTypeID = ab_remark_types.ID) WHERE (global_dates.publish_date)>='" . $start_date . "' and (global_dates.publish_date)<='" . $end_date . "' AND ab_bookings.pID in ($pubs) AND marketerID='" . $return['ID'] . "' AND deleted is null");


				//test_array($b);
				//$b = bookings::getAll("(global_dates.publish_date)>='" . $start_date . "' and (global_dates.publish_date)<='" . $end_date . "' AND ab_bookings.pID in ($pubs) AND marketerID='" . $return['ID'] . "' AND deleted is null");


			//	test_array($b);
				foreach ($b as $t) {
					$done = $done + $t['totalCost'];
				}



				foreach ($b as $record) {
					$publishDate = date("Y-m-d", strtotime($record['publishDate']));

					foreach ($targets as $ID => $target) {
						if ($publishDate >= $target['date_from'] && $publishDate <= $target['date_to'] && in_array($record['pID'], $target['pubs'])) {
							$targets[$ID]['done'] = $targets[$ID]['done'] + $record['totalCost'];
							$targets[$ID]['records'] = $targets[$ID]['records'] + 1;
						}

					}

				}

				$a = array();
				$next = array();
				foreach ($targets as $target) {
					$percent = number_format(($target['done'] / $target['target']) * 100, 2);
					$target['percent'] = $percent;
					$target['target'] = currency($target['target'],$user['company']['language'],$user['company']['currency']);
					$target['done'] = currency($target['done'],$user['company']['language'],$user['company']['currency']);
					if (!$next && $percent <= 100) {
						$next = $target;
					}

					$a[] = $target;
				}
				$targets = $a;


				$return['targets'] = $targets;
				$return['next_targets'] = $next;
			}


		} else {
			$return = "";
		}

		

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getAll($where = "", $orderby = "", $limit = "") {
		$timer = new timer();
		$f3 = \Base::instance();

		$user = $f3->get("user");
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


		$result = $f3->get("DB")->exec("
			SELECT DISTINCT ab_marketers_targets.*, if ((SELECT count(ID) FROM ab_marketers_targets_pub WHERE ab_marketers_targets_pub.mtID = ab_marketers_targets.ID AND ab_marketers_targets_pub.pID = '$pID' LIMIT 0,1)<>0,1,0) AS currentPub
			FROM ab_marketers_targets
			$where
			$orderby
			$limit
		");
		$t = array();
		foreach ($result as $r) {
			$pubs = ($f3->get("DB")->exec("SELECT pID FROM ab_marketers_targets_pub WHERE mtID = '" . $r['ID'] . "'"));
			$ps = array();
			foreach ($pubs as $pub) $ps[] = $pub['pID'];

			$r['pubs'] = implode(",", $ps);
			$t[] = $r;
		}
		$result = $t;


		$return = $result;
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getAll_count($where = "") {
		$timer = new timer();
		$f3 = \Base::instance();
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}


		$result = $f3->get("DB")->exec("
			SELECT count(ID) AS count
			FROM ab_marketers_targets
			$where
		");

		if (count($result)) {
			$return = $result[0]['count'];

		} else {
			$return = 0;
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
		$a = new \DB\SQL\Mapper($f3->get("DB"),"ab_marketers_targets");
		$a->load("ID='$ID'");


		foreach ($values as $key => $value) {
			$old[$key] = isset($a->$key) ? $a->$key : "";
			if (isset($a->$key)){

				$a->$key = $value;
			}

		}
		if (!$a->dry()) {
			$label = "Record Edited (" . currency($a->target,$user['company']['language'],$user['company']['currency']) . ")";
		} else {
			$label = "Record Added (" . currency($values['target'],$user['company']['language'],$user['company']['currency']) . ')';
		}
		$a->save();


		$ID = $a->ID;

		$cID = isset($values['cID']) ? $values['cID'] : "";
		if (!$cID) {
			$cID = $user['company']['ID'];
		}

		$publications = \models\publications::getAll("cID='$cID'", "publication ASC");


		//test_array($publications);
		$p = new \DB\SQL\Mapper($f3->get("DB"),"ab_marketers_targets_pub");
		$pub = array(
			"a" => array(),
			"r" => array()
		);

		foreach ($publications as $publication) {
			$p->load("pID='" . $publication['ID'] . "' AND mtID='" . $ID . "'");
			if (in_array($publication['ID'], $values['publications'])) {
				$p->pID = $publication['ID'];
				$p->mtID = $ID;
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

		$overwrite = array();
		if (count($str)) {
			$pub = array(
				"k" => "publications",
				"v" => implode(" | ", $str),
				"w" => '-'
			);
			$overwrite['publications'] = $pub;
		}


		//test_array($changes);


		//test_array($new_logging);


		\models\logging::_log("marketers_targets", $label, $values, $old, $overwrite, $lookupColumns);

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $ID;

	}

	public static function _delete($ID) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");


		$a = new \DB\SQL\Mapper($f3->get("DB"),"ab_marketers_targets");
		$a->load("ID='$ID'");

		$a->erase();


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return "done";

	}


	private static function dbStructure() {
		$f3 = \Base::instance();
		$table = $f3->get("DB")->exec("EXPLAIN ab_marketers;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}

		return $result;
	}
}