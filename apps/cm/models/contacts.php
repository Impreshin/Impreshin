<?php

namespace apps\cm\models;
use \timer as timer;

class contacts {
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
			SELECT cm_contacts.*,
				CONCAT(cm_contacts.firstName,' ',cm_contacts.lastName) as fullName

			FROM cm_contacts
			WHERE cm_contacts.ID = '$ID';

		"
		);
		if (count($result)) {
			$return = self::localization($result[0]);
			$return['logs'] = self::getLogs($return['ID']);


			
		} else {
			$return = $this->dbStructure;
		}
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args()
		);

		return $return;
	}

	

	

	public static function getAll($where = "", $grouping = array("g" => "none", "o" => "ASC"), $ordering = array("c" => "company", "o" => "ASC"), $options = array("limit" => "")) {
		$f3 = \Base::instance();
		$timer = new timer();
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}
		$order = self::order($grouping, $ordering);
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
		


		$sql = "
			SELECT cm_contacts.* ,
				CONCAT(cm_contacts.firstName,' ',cm_contacts.lastName) as fullName
			$select
			FROM cm_contacts
			$where
			$orderby
			$limit
		";
		if (isset($_GET['sql'])){
			echo $sql;
			exit();
		}

		$result = $f3->get("DB")->exec($sql);
		$return = $result;

		//test_array($return); 
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());

		return $return;
	}

	private static function localization($record) {
		$f3 = \Base::instance();
		$user = $f3->get("user");
		if (is_array($record)) {
			


		}

		return $record;
	}
	
	public static function display($data){
		$f3 = \Base::instance();
		$timer = new timer();
		$user = $f3->get("user");
		$return = array();
		$single = false;
		if (isset($data['ID'])){
			$data = array($data);
			$single = true;
		}

		if (is_array($data)) {
			$a = array();
			foreach ($data as $item) {
				$item['linkID']="pe-".$item['ID'];




				$item = self::localization($item);
				$return[] = $item;
			}
			
		}

		if ($single){
			$return = $return[0];
		}

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function display_list($data, $options = array("highlight" => "", "filter" => "*")) {
		$f3 = \Base::instance();
		if (!isset($options['highlight'])) $options['highlight'] = "";
		if (!isset($options['filter'])) $options['filter'] = "";
		$timer = new timer();
		$user = $f3->get("user");
		$permissions = $user['permissions'];
		$single = false;
		if (isset($data['ID'])){
			$data = array($data);
			$single = true;
		}

		$data = self::display($data);
		
		$return = array();
		$a = array();
		$groups = array();
		foreach ($data as $record) {
			
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
			//	test_array($permissions);
//echo $record[$options["highlight"]] . " | " . $showrecord . " | " . $options["filter"]. "<br>";
			if ($showrecord) {
				if (!isset($a[$record['heading']])) {
					$groups[] = $record['heading'];
					$arr = array(
						"heading" => $record['heading'], 
						"count" => ""
					);
					$arr['groups'] = "";
					$arr['records'] = "";
					$a[$record['heading']] = $arr;
				}
				
				
				$a[$record['heading']]["records"][] = $record;
			}
		}
		$return = array();
		foreach ($a as $record) {
			$record['count'] = count($record['records']);
			
			$record['groups'] = $groups;
			$return[] = $record;
		}
		
		
		if ($single){
			$return = $return[0];
		}
		
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args()
		);

		return $return;
	}

	private static function order($grouping, $ordering) {
		$f3 = \Base::instance();
		$o = explode(".", $ordering['c']);
		$a = array();
		foreach ($o as $b) {
			$a[] = "" . $b . "";
		}
		$a = implode(".", $a);
		$orderby = " " . $a . " " . $ordering['o'];
		$arrange = "";
		$ordering = isset($grouping['o'])?$grouping['o']:"ASC";
		$grouping = isset($grouping['g'])?$grouping['g']:'none';
		switch ($grouping) {
			case "az":
				$orderby = "COALESCE(LEFT(cm_contacts.firstName, 1),'zzzzzzz') $ordering, " . $orderby;
				$arrange = "COALESCE(LEFT(cm_contacts.firstName, 1),'None') as heading";
				break;
			case "az_last":
				$orderby = "COALESCE(LEFT(cm_contacts.lastName, 1),'zzzzzzz') $ordering, " . $orderby;
				$arrange = "COALESCE(LEFT(cm_contacts.lastName, 1),'None') as heading";
				break;
			
			case "none":
				$orderby = "" . $orderby;
				$arrange = "'None' as heading";
				break;
		}

		//test_array($orderby);
		return array("order" => $orderby, "select" => $arrange);
	}

	public static function _delete($ID = "", $reason = "") {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		$userID = $user['ID'];
		$a = new \DB\SQL\Mapper($f3->get("DB"), "cm_contacts");
		$a->load("ID='$ID'");
		
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args()
		);

		return "deleted";
	}

	

	public static function save($ID = "", $values = array(), $opts = array("dry" => true, "section" => "contacts")) {
		//test_array($values);
		$timer = new timer();
		$f3 = \Base::instance();
		$lookupColumns = array();
		
		$lookup = array();
		$a = new \DB\SQL\Mapper($f3->get("DB"), "cm_contacts");
		$a->load("ID='$ID'");
		//test_array($cfg);
		$user = $f3->get("user");
		
		$changes = array();
		$material = false;
		foreach ($values as $key => $value) {
			if (strpos($key, "aterial_")) $material = true;
			if (isset($a->$key)) {
				$cur = $a->$key;
				if (is_numeric($cur)) $cur = $cur + 0;


				//$cur = $a->$key;
				if ($cur !== $value) {
					if (isset($lookupColumns[$key])) {
						$lookupColumns[$key]['val'] = $value;
						$lookupColumns[$key]['was'] = $cur;
						$lookup[] = $lookupColumns[$key];
					} else {
						$w = $cur;
						$v = $value;
						
						$changes[] = array("k" => $key, "v" => $v, "w" => str_replace("0000-00-00 00:00:00", "", $w));
					}
				}
				$a->$key = $value;
			}
		}
		if (count($changes)){
			$a->dateChanged = date("Y-m-d H:i:s");
		}
		if ($opts['dry'] || !$a->dry()) {
			$a->save();
		}
		if (!$ID) {
			$label = "Company Added";
			$ID = $a->ID;
		} else {
			$label = "Company Edited";
		}
		$sql = "SELECT 1 ";
		
		foreach ($lookup as $col) {
			$sql .= ", " . str_replace("{val}", $col['val'], $col['sql']) . " AS " . $col['col'];
			$sql .= ", " . str_replace("{val}", $col['was'], $col['sql']) . " AS " . $col['col'] . "_was";
		}
		$v = $f3->get("DB")->exec($sql);
		$v = $v[0];
		foreach ($lookup as $col) {
			$was = $v[$col['col'] . "_was"];
			if (is_numeric($was)) $was = $was + 0;
			$changes[] = array("k" => $col['col'], "v" => $v[$col['col']], "w" => $was);
		}

		if (isset($opts['section']) && $opts['section']) {
			switch ($opts['section']) {
				case "company":
					if ($a->material_status == '1') {
						$label = "Material - Ready";
						if ($a->material_source == '1') {
							$production = (isset($v['production'])) ? $v['production'] : "";
							if ($production) $label .= " (" . $production . ")";
						} else {
							$label .= " (Supplied)";
						}
					} else {
						$label = "Material - Not Ready";
					}
					break;
				
			}
		}
		//test_array(array("changes"=>$changes,"label"=>$label)); 
		if (count($changes)) self::logging($ID, $changes, $label);
		$n = new contacts();
		$n = $n->get($ID);
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args()
		);

		return $n;
	}

	private static function getLogs($ID) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		$return = $f3->get("DB")->exec("SELECT *, (SELECT fullName FROM global_users WHERE global_users.ID =cm_contacts_logs.userID ) AS fullName FROM cm_contacts_logs WHERE contactID = '$ID' ORDER BY datein DESC");
		$a = array();
		foreach ($return as $record) {
			$record['log'] = json_decode($record['log']);
			$record['datein'] = datetime($record['datein'],'',$user['company']['timezone']);
			$a[] = $record;
		}
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args()
		);

		return $a;
	}

	private static function logging($ID, $log = array(), $label = "Log") {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		$userID = $user['ID'];
		$log = (json_encode($log));
		$log = str_replace("'", "\\'", $log);
		$log = str_replace('"', '\\"', $log);
		$f3->get("DB")->exec("INSERT INTO cm_cotacts_logs (`contactID`, `log`, `label`, `userID`) VALUES ('$ID','$log','$label','$userID')");
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args()
		);
	}

	public static function dbStructure() {
		$f3 = \Base::instance();
		$table = $f3->get("DB")->exec("EXPLAIN cm_contacts;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}
		

		return $result;
	}
}