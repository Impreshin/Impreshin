<?php

namespace models\nf;

use \F3 as F3;
use \Axon as Axon;
use \timer as timer;

class articles {
	private $classname;

	function __construct() {

		$classname = get_class($this);
		$this->dbStructure = $classname::dbStructure();

	}

	function get($ID) {
		$timer = new timer();
		$user = F3::get("user");
		$userID = $user['ID'];
		$currentDate = $user['publication']['current_date'];

		$currentDate = $currentDate['publish_date'];


		//test_array($currentDate);

		$result = F3::get("DB")->exec("
			SELECT nf_articles.*
			FROM nf_articles
			WHERE nf_articles.ID = '$ID';
		");


		if (count($result)) {
			$return = ($result[0]);
			//$return['publishDateDisplay'] = date("d F Y", strtotime($return['publish_date']));
			$return['logs'] = articles::getLogs($return['ID']);
			$return['state'] = "";

			if ($return['publish_date'] == $currentDate) {
				$return['state'] = "Current";
			} elseif ($return['publish_date'] < $currentDate) {
				$return['state'] = "Archived";
			} elseif ($return['publish_date'] > $currentDate) {
				$return['state'] = "Future";
			}

			$cfg = F3::get("cfg");
			$cfg = $cfg['upload'];


		} else {
			$return = $this->dbStructure;
		}
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getAll_count($where = "") {
		$timer = new timer();
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}


		$return = F3::get("DB")->exec("
			SELECT count(nf_articles.ID) as records
			FROM nf_articles
			$where
		");
		if (count($return)) {
			$return = $return[0]['records'];
		}

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getAll_select($select, $where = "", $orderby, $groupby = "") {
		/*
				return array(
					"select"=>$select,
					"where"=>$where,
					"orderby"=>$orderby,
					"group"=>$groupby
				);
		*/
		$timer = new timer();
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}

		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}
		if ($groupby) {
			$groupby = " GROUP BY " . $groupby;
		}


		$return = F3::get("DB")->exec("
			SELECT $select
			FROM nf_articles


			$where
$groupby
			$orderby
		");


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getAll($where = "", $grouping = array(
		"g" => "none",
		"o" => "ASC"
	), $ordering = array("c" => "client", "o" => "ASC"), $options = array("limit" => "")) {
		$timer = new timer();

		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}
		$order = articles::order($grouping, $ordering);
		$orderby = $order['order'];
		$select = $order['select'];
		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}
		if ($select) {
			$select = " ," . $select;
		}

		if ($options['limit']) {
			if (strpos($options['limit'], "LIMIT") == -1) {
				$limit = " LIMIT " . $options['limit'];
			} else {
				$limit = $options['limit'];
			}

		} else {
			$limit = " ";
		}


		$result = F3::get("DB")->exec("
			SELECT nf_articles.*
			$select

			FROM nf_articles
			$where
			$orderby
			$limit
		");


		$return = $result;

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}


	public static function display($data, $options = array("highlight" => "", "filter" => "*")) {
		if (!isset($options['highlight'])) $options['highlight'] = "";
		if (!isset($options['filter'])) $options['filter'] = "";


		$timer = new timer();
		$user = F3::get("user");
		$permissions = $user['permissions'];
		if (is_array($data)) {
			$a = array();


			foreach ($data as $item) {


				$a[] = ($item);
			}
			$data = $a;

		}


		$return = array();
		$a = array();
		$groups = array();
		foreach ($data as $record) {
			if (isset($user['permissions']['fields'])) {
				foreach ($user['permissions']['fields'] as $key => $value) {
					if ($value == 0) {
						if (isset($record[$key])) unset($record[$key]);
						if (isset($record[$key . "_C"])) unset($record[$key . "_C"]);
					}
				}
			}

			$showrecord = true;
			if (isset($options["highlight"]) && $options["highlight"]) {
				$record['highlight'] = $record[$options["highlight"]];
			}


			if (isset($options["filter"])) {
				if ($options["filter"] == "*") {
					$showrecord = true;
				} else {
					if (isset($record[$options["highlight"]]) && $record[$options["highlight"]] == $options['filter']) {
						$showrecord = true;
					} else {
						$showrecord = false;
					}

				}
			}


//echo $record[$options["highlight"]] . " | " . $showrecord . " | " . $options["filter"]. "<br>";
			if ($showrecord) {
				if (!isset($a[$record['heading']])) {
					$groups[] = $record['heading'];

					$arr = array(
						"heading" => $record['heading'],
						"count"   => "",

					);
					$arr['groups'] = "";
					$arr['records'] = "";


					$a[$record['heading']] = $arr;
				}


				if (isset($permissions['lists']['fields'])) {
					foreach ($permissions['lists']['fields'] as $key => $value) {
						if ($value == 0) {
							if (isset($record[$key])) unset($record[$key]);
							if (isset($record[$key . "_C"])) unset($record[$key . "_C"]);
						}
					}
				}


				$a[$record['heading']]["records"][] = $record;
			}
		}

		$return = array();


//exit();
		foreach ($a as $record) {
			$record['count'] = count($record['records']);


			$record['groups'] = $groups;
			$return[] = $record;
		}
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;

	}

	private static function order($grouping, $ordering) {

		$o = explode(".", $ordering['c']);
		$a = array();
		foreach ($o as $b) {
			$a[] = "`" . $b . "`";
		}
		$a = implode(".", $a);
		$orderby = " " . $a . " " . $ordering['o'];
		$arrange = "";
		$ordering = $grouping['o'];
		switch ($grouping['g']) {
			case "type":
				$orderby = "COALESCE(ab_bookings_types.orderby,99999) $ordering, " . $orderby;
				$arrange = "COALESCE(ab_bookings_types.type,ab_bookings_types.type) as heading";
				break;
			case "date":
				$orderby = "COALESCE(global_dates.publish_date,99999) $ordering, " . $orderby;
				$arrange = "DATE_FORMAT(global_dates.publish_date, '%d %M %Y' ) as heading";
				break;
			case "placing":
				$orderby = "COALESCE(ab_placing.orderby,99999) $ordering,  ab_bookings_types.orderby," . $orderby;
				$arrange = "COALESCE(ab_placing.placing,ab_bookings_types.type) as heading";
				break;
			case "marketer":
				$orderby = "COALESCE(ab_marketers.marketer,'zzzzzzzzz') $ordering, " . $orderby;
				$arrange = "COALESCE(ab_marketers.marketer,'None') as heading";
				break;
			case "columns":
				$orderby = "if(typeID='1',ab_bookings.col,99999) $ordering, ab_bookings_types.orderby, " . $orderby;
				$arrange = "if(typeID='1',concat('Columns: ',ab_bookings.col),ab_bookings_types.type) as heading";
				break;
			case "pages":
				$orderby = "if(typeID='1',global_pages.page,99999) $ordering, ab_bookings_types.orderby, " . $orderby;
				$arrange = "if(typeID='1',COALESCE(concat('Page: ',format(global_pages.page,0)),'Not Planned Yet'),ab_bookings_types.type) as heading";
				break;
			case "colours":
				$orderby = "if(typeID='1',COALESCE(ab_colour_rates.colour,'zzzzzzzzz'),'zzzzzzzzz') $ordering, ab_bookings_types.orderby, " . $orderby;
				$arrange = "if(typeID='1',ab_colour_rates.colour,ab_bookings_types.type) as heading";
				break;
			case "discountPercent":
				$orderby = "if((totalShouldbe<>totalCost) AND totalShouldbe>0,if(((totalShouldbe - totalCost))>0,1,2),0) $ordering, " . $orderby;
				$arrange = "if((totalShouldbe<>totalCost) AND totalShouldbe>0,if(((totalShouldbe - totalCost))>0,'Under Charged','Over Charged'),'No Discount') as heading";
				break;
			case "accountStatus":
				$orderby = "COALESCE(ab_accounts_status.orderby,99999) $ordering,  ab_bookings_types.orderby, " . $orderby;
				$arrange = "if(ab_accounts_status.status<>'',concat('Account - ',ab_accounts_status.status),ab_bookings_types.type) as heading";
				break;

			case "material_production":
				$orderby = "if(typeID='1',(CASE material_source WHEN 1 THEN 0 WHEN 2 THEN 1 END),99999) $ordering, ab_bookings_types.orderby, ab_bookings.material_production $ordering,  " . $orderby;
				$arrange = "if(typeID='1',COALESCE((CASE material_source WHEN 1 THEN ab_bookings.material_production WHEN 2 THEN 'Supplied' END),'None'),ab_bookings_types.type) as heading";
				break;
			case "invoicedStatus":
				$orderby = "if (invoiceNum,1,0) $ordering, " . $orderby;
				$arrange = "if (invoiceNum,'Invoiced','Not Invoiced') as heading";
				break;


			case "none":
				$orderby = "" . $orderby;
				$arrange = "'None' as heading";
				break;

		}


		return array(
			"order" => $orderby,
			"select" => $arrange
		);
	}

	public static function _delete($ID = "", $reason = "") {
		$timer = new timer();

		$user = F3::get("user");
		$userID = $user['ID'];


		$a = new Axon("nf_articles");
		$a->load("ID='$ID'");

		if (!$a->dry()) {
			$a->deleted = "1";
			$a->deleted_userID = $userID;
			$a->deleted_user = $user['fullName'];
			$a->deleted_date = date("Y-m-d H:i:s");
			$a->deleted_reason = ($reason);

			$a->save();
			$changes = array(
				array(
					"k" => "Deleted",
					"v" => "1",
					"w" => ""
				),
				array(
					"k" => "deleted_user",
					"v" => $user['fullName'],
					"w" => ""
				),
				array(
					"k" => "deleted_reason",
					"v" => $reason,
					"w" => ""
				)
			);

			articles::logging($a->ID, $changes, "Article Deleted");
		}


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return "deleted";
	}

	public static function save($ID = "", $values = array(), $opts = array("dry" => true, "section" => "booking")) {

		//test_array($values);
		$timer = new timer();
		$lookupColumns = array();
		/*
		$lookupColumns["dID"] = array("sql"=>"(SELECT publish_date FROM global_dates WHERE ID = '{val}')","col"=>"publish_date","val"=>"");
		$lookupColumns["placingID"] = array("sql"=>"(SELECT placing FROM ab_placing WHERE ID = '{val}')","col"=>"placing","val"=>"");
		$lookupColumns["categoryID"] = array("sql"=>"(SELECT `category` FROM ab_categories WHERE ID = '{val}')","col"=>"category","val"=>"");
		$lookupColumns["marketerID"] = array("sql"=>"(SELECT `marketer` FROM ab_marketers WHERE ID = '{val}')","col"=>"marketer","val"=>"");
		$lookupColumns["colourID"] = array("sql"=>"(SELECT `colour` FROM ab_colour_rates WHERE ID = '{val}')","col"=>"colour","val"=>"");
		$lookupColumns["material_productionID"] = array("sql"=>"(SELECT `production` FROM ab_production WHERE ID = '{val}')","col"=>"production","val"=>"");
		$lookupColumns["remarkTypeID"] = array("sql"=>"(SELECT `remarkType` FROM ab_remark_types WHERE ID = '{val}')","col"=>"remarkType","val"=>"");
		$lookupColumns["checked_userID"] = array("sql"=>"(SELECT `fullName` FROM global_users WHERE ID = '{val}')","col"=>"checked_user","val"=>"");
		$lookupColumns["material_source"] = array("sql"=>"(CASE '{val}' WHEN 1 THEN 'Production' WHEN 2 THEN 'Supplied' END)","col"=>"material_source","val"=>"");
		$lookupColumns["material_status"] = array("sql"=>"(CASE '{val}' WHEN 1 THEN 'Ready' WHEN 0 THEN 'Not Ready' END)","col"=>"material_status","val"=>"");
		$lookupColumns["checked"] = array("sql"=>"(CASE '{val}' WHEN 1 THEN 'Checked' WHEN 0 THEN 'Not Checked' END)","col"=>"checked","val"=>"");
		$lookupColumns["pageID"] = array("sql"=>"(SELECT TRUNCATE(`page`,0) FROM global_pages WHERE ID = '{val}')","col"=>"page","val"=>"");
		$lookupColumns["accountID"] = array("sql"=>"(SELECT concat(accNum,' | ',account) FROM ab_accounts WHERE ID = '{val}')","col"=>"Account","val"=>"");
		*/
		$lookup = array();


		$a = new Axon("nf_bookings");
		$a->load("ID='$ID'");


		$cfg = F3::get("cfg");
		$cfg = $cfg['upload'];
		//test_array($cfg);

		$user = F3::get("user");
		$cID = $user['publication']['cID'];


		/*
				if (($cfg['material'] && $user['company']['ab_upload_material'] == '1' && $user['publication']['ab_upload_material'] == '1') && !$a->dry()) {
					if ($a->material_file_store){
						$oldFolder = $cfg['folder'] . "ab/" . $cID . "/" . $a->pID . "/" . $a->dID . "/material/";


						if ((isset($values['material_status']) && $values['material_status'] == "0" && $a->material_file_store) || (isset($values['material_file_store']) && $a->material_file_store != $values['material_file_store'])) {

							if (file_exists($oldFolder . $a->material_file_store)) {
								@unlink($oldFolder . $a->material_file_store);
							}
						} else {






						if (isset($values['dID'])) {

							//echo "old: " . $oldFolder . $a->material_file_store . "<br>";
							if (file_exists($oldFolder. $a->material_file_store)){



								$newFolder = $cfg['folder'] . "ab/" . $cID . "/" . $a->pID . "/" . $values['dID'] . "/material/";


								//echo "new: ". $newFolder . $a->material_file_store ."<br>";

								if (!file_exists($newFolder)) @mkdir($newFolder, 0777, true);

								@rename($oldFolder . $a->material_file_store, $newFolder . $a->material_file_store);
							}


						}
						}



					}


				}
		*/


		$changes = array();
		$material = false;
		foreach ($values as $key => $value) {

			$cur = $a->$key;
			if ($cur != $value) {
				if (isset($lookupColumns[$key])) {
					$lookupColumns[$key]['val'] = $value;
					$lookupColumns[$key]['was'] = $cur;
					$lookup[] = $lookupColumns[$key];
				} else {
					$w = $cur;
					$v = $value;
					if ($key == "material_file_filesize") {
						$v = $v ? file_size($v) : "";
						$w = $w ? file_size($w) : "";
					}
					$changes[] = array(
						"k" => $key,
						"v" => $v,
						"w" => str_replace("0000-00-00 00:00:00", "", $w)
					);
				}

			}
			$a->$key = $value;
		}

		if ($opts['dry'] || !$a->dry()) {
			$a->save();
		}


		if (!$ID) {
			$label = "Article Added";
			$ID = $a->_id;
		} else {
			$label = "Article Edited";
		}

		$sql = "SELECT 1 ";

		foreach ($lookup as $col) {
			$sql .= ", " . str_replace("{val}", $col['val'], $col['sql']) . " AS " . $col['col'];
			$sql .= ", " . str_replace("{val}", $col['was'], $col['sql']) . " AS " . $col['col'] . "_was";
		}


		$v = F3::get("DB")->exec($sql);
		$v = $v[0];
		foreach ($lookup as $col) {
			$changes[] = array(
				"k" => $col['col'],
				"v" => $v[$col['col']],
				"w" => $v[$col['col'] . "_was"]
			);
		}


		if (count($changes)) articles::logging($ID, $changes, $label);


		$n = new bookings();
		$n = $n->get($ID);


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $n;
	}

	private static function getLogs($ID) {
		$timer = new timer();

		$return = F3::get("DB")->exec("SELECT *, (SELECT fullName FROM global_users WHERE global_users.ID =ab_bookings_logs.userID ) AS fullName FROM nf_articles_logs WHERE bID = '$ID' ORDER BY datein DESC");
		$a = array();
		foreach ($return as $record) {
			$record['log'] = json_decode($record['log']);
			$a[] = $record;
		}

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $a;
	}

	private static function logging($ID, $log = array(), $label = "Log") {
		$timer = new timer();
		$user = F3::get("user");
		$userID = $user['ID'];


		$log = mysql_escape_string(json_encode($log));
		//	$log = str_replace("'", "\\'", $log);


		F3::get("DB")->exec("INSERT INTO nf_articles_logs (`aID`, `log`, `label`, `userID`) VALUES ('$ID','$log','$label','$userID')");

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
	}

	public static function dbStructure() {
		$table = F3::get("DB")->exec("EXPLAIN nf_articles;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}
		$result["heading"] = "";
		$result["publishDateDisplay"] = "";
		$result["checked"] = "";
		$result["material"] = "";
		$result["layout"] = "";
		return $result;
	}
}