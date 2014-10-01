<?php

namespace apps\cm\models;
use \timer as timer;

class companies {
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
			SELECT cm_companies.*
			FROM cm_companies

			WHERE cm_companies.ID = '$ID';

		"
		);
		if (count($result)) {
			$return = $result[0];

			$app_settings = \apps\cm\settings::_available();
			$types = array();
			foreach ($app_settings['types'] as $i){
				$types[$i['ID']] = $i;
			}
			
			
			$details = $f3->get("DB")->exec("
				SELECT cm_companies_details.*
				FROM cm_companies_details 
				WHERE parentID = '{$return['ID']}'
				ORDER BY orderby ASC;"
			);
			$d = array();
			foreach ($details as $item){

				$item['icon']=isset($types[$item['catID']]['icon'])?$types[$item['catID']]['icon']:"";
				$item['type']=isset($types[$item['catID']]['type'])?$types[$item['catID']]['type']:"";
				
				$d[$item['group']]["group"] = $item['group'];
				$d[$item['group']]["records"][] = $item;
				
			}
			$c = array();
			foreach ($d as $item){
				$c[] = $item;
			}
			$details = ($c); 
			
			
			$return['details'] =$details;


			$linked_co = array();
			$linked_pe = array();
			
			$ID = $return['ID'];

			$detailss = $f3->get("DB")->exec("
				SELECT GROUP_CONCAT(CONCAT(cm_companies_links_company.linkedID,',',cm_companies_links_company.parentID) SEPARATOR ',') as links
				FROM cm_companies_links_company 
				WHERE parentID = '{$ID}' OR linkedID = '{$ID}'
			");
			if (count($detailss)){
				$detailss = $detailss[0]['links'];
				if ($detailss) $linked_co = self::getAll("ID in ($detailss) AND ID != '{$ID}'");
			}
			
			$detailss = $f3->get("DB")->exec("
				SELECT GROUP_CONCAT((cm_companies_links_contact.linkedID) SEPARATOR ',') as links
				FROM cm_companies_links_contact 
				WHERE parentID = '{$ID}'
			");
			
			if (count($detailss)){
				$detailss = $detailss[0]['links'];
				if ($detailss) $linked_pe = contacts::getAll("ID in ($detailss)");
			}
			
			


			$return['linked']['company'] =$linked_co;
			$return['linked']['contact'] =$linked_pe;
			
			$return = self::localization($return);
			$return['logs'] = self::getLogs($return['ID']);

			
			
		} else {
			$return = $this->dbStructure;
			$return['details'] = array();

		
		}
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args()
		);

		return $return;
	}



	public static function getAll($where = "", $orderby = "cm_companies.company ASC",$limit="") {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");

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
		
		//test_array($where);

		
		$result = $f3->get("DB")->exec("
			SELECT cm_companies.* 
			FROM cm_companies
			$where
			$orderby
			$limit
		");
		
		$return = $result;
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getList($where = "", $grouping = array("g" => "none", "o" => "ASC"), $ordering = array("c" => "company", "o" => "ASC"), $options = array("limit" => "")) {
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
			SELECT cm_companies.* 
			$select
			FROM cm_companies
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

		$app_settings = \apps\cm\settings::_available();
		$types = array();
		foreach ($app_settings['types'] as $i){
			$types[$i['ID']] = $i;
		}


		if (is_array($data)) {
			$a = array();
			foreach ($data as $item) {
				$item['linkID']="co-".$item['ID'];




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
				$orderby = "COALESCE(LEFT(cm_companies.company, 1),'zzzzzzz') $ordering, " . $orderby;
				$arrange = "COALESCE(LEFT(UPPER(cm_companies.company), 1),'None') as heading";
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
		$a = new \DB\SQL\Mapper($f3->get("DB"), "cm_companies");
		$a->load("ID='$ID'");
		
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args()
		);

		return "deleted";
	}

	

	public static function save($ID = "", $values = array(), $opts = array("dry" => true, "section" => "companies")) {
		//test_array($values);
		$timer = new timer();
		$f3 = \Base::instance();
		$lookupColumns = array();
		
		$lookup = array();
		$a = new \DB\SQL\Mapper($f3->get("DB"), "cm_companies");
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

		
		if (isset($values['details'])){
			$b = new \DB\SQL\Mapper($f3->get("DB"), "cm_companies_details");
			foreach($values['details'] as $item){
				$b->load("ID='{$item['ID']}'");
				if ($item['value']=="" && !$b->dry()){
					$b->erase();
				} else {
					$b->parentID = $ID;
					$b->catID = $item['catID'];
					$b->value = $item['value'];
					$b->group = $item['group'];
					$b->orderby = $item['orderby'];

					$b->save();
				}
				$b->reset();
				
				
			}
		}

		if (isset($values['linked'])){
			if (isset($values['linked']['company'])){
				$f3->get("DB")->exec("DELETE FROM cm_companies_links_company WHERE parentID = '{$ID}' OR linkedID='{$ID}'");
				foreach ($values['linked']['company'] as $item){
					$f3->get("DB")->exec("INSERT INTO cm_companies_links_company (parentID,linkedID) VALUES ('{$ID}','{$item}')");
				}
			}
			if (isset($values['linked']['contact'])){
				$f3->get("DB")->exec("DELETE FROM cm_companies_links_contact WHERE parentID = '{$ID}'");
				foreach ($values['linked']['contact'] as $item){
					$f3->get("DB")->exec("INSERT INTO cm_companies_links_contact (parentID,linkedID) VALUES ('{$ID}','{$item}')");
				}
			}
			
		}
		
		
		
		//test_array(array("changes"=>$changes,"label"=>$label)); 
		if (count($changes)) self::logging($ID, $changes, $label);
		$n = new companies();
		$n = $n->get($ID);
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args()
		);

		return $n;
	}

	private static function getLogs($ID) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		$return = $f3->get("DB")->exec("SELECT *, (SELECT fullName FROM global_users WHERE global_users.ID =cm_companies_logs.userID ) AS fullName FROM cm_companies_logs WHERE parentID = '$ID' ORDER BY datein DESC");
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
		$f3->get("DB")->exec("INSERT INTO cm_companies_logs (`parentID`, `log`, `label`, `userID`) VALUES ('$ID','$log','$label','$userID')");
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args()
		);
	}

	public static function dbStructure() {
		$f3 = \Base::instance();
		$table = $f3->get("DB")->exec("EXPLAIN cm_companies;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}
		

		return $result;
	}
}