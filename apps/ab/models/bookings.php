<?php

namespace apps\ab\models;
use \timer as timer;

class bookings {
	private $classname;

	function __construct() {
		$classname = get_class($this);
		$this->dbStructure = $classname::dbStructure();
	}

	private static function _from() {
		$return = "(((((((((((((((((ab_bookings LEFT JOIN ab_placing ON ab_bookings.placingID = ab_placing.ID) LEFT JOIN ab_bookings_types ON ab_bookings.typeID = ab_bookings_types.ID) LEFT JOIN ab_marketers ON ab_bookings.marketerID = ab_marketers.ID) LEFT JOIN ab_categories ON ab_bookings.categoryID = ab_categories.ID) LEFT JOIN global_users ON ab_bookings.userID = global_users.ID) LEFT JOIN global_publications ON ab_bookings.pID = global_publications.ID) LEFT JOIN ab_accounts ON ab_bookings.accountID = ab_accounts.ID) LEFT JOIN global_dates ON ab_bookings.dID = global_dates.ID) LEFT JOIN ab_accounts_status ON ab_accounts.statusID = ab_accounts_status.ID) INNER JOIN ab_remark_types ON ab_bookings.remarkTypeID = ab_remark_types.ID) LEFT JOIN global_pages ON ab_bookings.pageID = global_pages.ID) LEFT JOIN ab_placing_sub ON ab_bookings.sub_placingID = ab_placing_sub.ID) LEFT JOIN ab_inserts_types ON ab_bookings.insertTypeID = ab_inserts_types.ID) LEFT JOIN system_publishing_colours ON ab_bookings.colourID = system_publishing_colours.ID) LEFT JOIN ab_production ON ab_bookings.material_productionID = ab_production.ID) LEFT JOIN system_publishing_colours AS system_publishing_colours_1 ON ab_placing.colourID = system_publishing_colours_1.ID) LEFT JOIN system_publishing_colours AS system_publishing_colours_2 ON ab_placing_sub.colourID = system_publishing_colours_2.ID) LEFT JOIN system_payment_methods ON ab_bookings.payment_methodID = system_payment_methods.ID";

		return $return;
	}

	function get($ID) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		$userID = $user['ID'];
		$currentDate = $user['publication']['current_date'];
		$currentDate = $currentDate['publish_date'];
		//test_array($currentDate);
		$from = self::_from();
		$result = $f3->get("DB")->exec("
			SELECT ab_bookings.*,
				ab_placing.placing AS placing,

				ab_bookings_types.type AS type,
				ab_marketers.marketer AS marketer, ab_marketers.code AS marketerCode,
				global_publications.publication AS publication,
				global_publications.cID AS cID,
				global_dates.publish_date AS publish_date, if(global_dates.publish_date<$currentDate,'0','1') AS dateStatus,
				ab_categories.category AS category,
				global_users.fullName AS byFullName,
				ab_accounts.account AS account,ab_accounts.accNum AS accNum,
				ab_accounts_status.blocked AS accountBlocked, ab_accounts_status.labelClass,ab_accounts_status.status AS accountStatus,
				ab_accounts.accNum AS accNum,
				ab_remark_types.remarkType, ab_remark_types.labelClass AS remarkTypeLabelClass,
				global_pages.page,
				ab_production.production AS material_production,
				system_payment_methods.label AS payment_method,
				DATE_FORMAT(ab_bookings.datein, '%Y-%m-%d' ) AS datein_date,

				if(ab_placing_sub.placingID=ab_bookings.placingID,ab_placing_sub.label,NULL) AS sub_placing,
COALESCE(if(ab_placing_sub.placingID=ab_bookings.placingID,system_publishing_colours_2.colour,NULL), system_publishing_colours_1.colour, system_publishing_colours.colour) AS colour,
COALESCE(if(ab_placing_sub.placingID=ab_bookings.placingID,system_publishing_colours_2.colourLabel,NULL), system_publishing_colours_1.colourLabel, system_publishing_colours.colourLabel) AS colourLabel,


				ab_inserts_types.insertsLabel AS insertLabel

			FROM $from


			WHERE ab_bookings.ID = '$ID';

		"
		);
		if (count($result)) {
			$return = bookings::localization($result[0]);
			$return['publishDateDisplay'] = date("d F Y", strtotime($return['publish_date']));
			$return['logs'] = bookings::getLogs($return['ID']);
			$return['state'] = "";

			if (isset($return['x_offset'])&&$return['x_offset'])$return['x_offset'] = $return['x_offset'] + 0;
			if (isset($return['y_offset'])&&$return['y_offset'])$return['y_offset'] = $return['y_offset'] + 0;
			
			
			if ($return['publish_date'] == $currentDate) {
				$return['state'] = "Current";
			} elseif ($return['publish_date'] < $currentDate) {
				$return['state'] = "Archived";
			} elseif ($return['publish_date'] > $currentDate) {
				$return['state'] = "Future";
			}
			if ($return['pageID'] && $return["page"]) {
				$return["page"] = number_format($return['page'], 0);
			}
			if (isset($user['permissions']['details']['fields'])) {
				foreach ($user['permissions']['details']['fields'] as $key => $value) {
					if ($value == 0) {
						if (isset($return[$key])) unset($return[$key]);
						if (isset($return[$key . "_C"])) unset($return[$key . "_C"]);
					}
				}
			}
			$cfg = $f3->get("CFG");
			$cfg = $cfg['upload'];
			$return['material_file_filesize_display'] = 0;
			
			
			
			if ($cfg['material'] && $user['company']['ab_upload_material'] == '1' && $user['publication']['ab_upload_material'] == '1') {
				if ($return['material_file_store']) {
					$file = $cfg['folder'] . "ab/" . $return['cID'] . "/" . $return['pID'] . "/" . $return['dID'] . "/material/" . $return['material_file_store'];
					if (!file_exists($file)) {
						$return['material_status'] = '0';
						$return['material_file_filename'] = '';
					} else {
						$return['material_file_filesize_display'] = file_size($return['material_file_filesize']);
					}
				}
			} else {
				$return['material_file_store'] = "";
				$return['material_file_filename'] = "";
				$return['material_file_filesize'] = "";
			}
		} else {
			$return = $this->dbStructure;
		}
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args()
		);

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
		$from = self::_from();
		$return = $f3->get("DB")->exec("
			SELECT count(ab_bookings.ID) AS records
			FROM $from
			$where
		"
		);
		if (count($return)) {
			$return = $return[0]['records'];
		}
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args()
		);

		return $return;
	}

	public static function getAll_select($select, $where = "", $orderby, $groupby = "") {
		/*
						test_array(array(
							"select"=>$select,
							"where"=>$where,
							"orderby"=>$orderby,
							"group"=>$groupby
						));
		*/
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
		if ($groupby) {
			$groupby = " GROUP BY " . $groupby;
		}
		$from = self::_from();
		$return = $f3->get("DB")->exec("
			SELECT $select
			FROM $from
			$where
			$groupby
			$orderby
		"
		);
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args()
		);

		return $return;
	}

	public static function getAll($where = "", $grouping = array("g" => "none", "o" => "ASC"), $ordering = array("c" => "client", "o" => "ASC"), $options = array("limit" => "")) {
		$f3 = \Base::instance();
		$timer = new timer();
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}
		$order = bookings::order($grouping, $ordering);
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
		$from = self::_from();
		
		
		$sql = "
			SELECT ab_bookings.*, ab_placing.placing, ab_bookings_types.type, ab_marketers.marketer,
			global_dates.publish_date AS publishDate,
			ab_placing_sub.label AS sub_placing,
				ab_accounts.account AS account, ab_accounts.accNum AS accNum, ab_accounts.email AS account_email, ab_accounts.phone AS account_phone, ab_accounts_status.blocked AS accountBlocked, ab_accounts_status.status AS accountStatus, ab_accounts_status.labelClass,
				ab_remark_types.remarkType, ab_remark_types.labelClass AS remarkTypeLabelClass,
				material_status AS material,
				CASE material_source WHEN 1 THEN 'Production' WHEN 2 THEN 'Supplied' END AS material_source,
				ab_production.production AS material_production,
				if (`page`,1,0) AS layout,
				format(global_pages.page,0) AS page,
				if(x_offset is not null and y_offset is not null,1,0) AS planned,
				ab_inserts_types.insertsLabel AS insertLabel,
				system_payment_methods.label AS payment_method,
				if(ab_placing_sub.placingID=ab_bookings.placingID,ab_placing_sub.label,NULL) AS sub_placing,
				COALESCE(if(ab_placing_sub.placingID=ab_bookings.placingID,system_publishing_colours_2.colour,NULL), system_publishing_colours_1.colour, system_publishing_colours.colour) AS colour,
				COALESCE(if(ab_placing_sub.placingID=ab_bookings.placingID,system_publishing_colours_2.colourLabel,NULL), system_publishing_colours_1.colourLabel, system_publishing_colours.colourLabel) AS colourLabel,
				DATE_FORMAT(ab_bookings.datein, '%Y-%m-%d' ) AS datein_date,
				now()  AS last_change
			$select
			FROM ($from )
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
			if (isset($record['colourCost']) && $record['colourCost']) $record['colourCost_C'] = currency($record['colourCost'],$user['company']['language'],$user['company']['currency']);
			if (isset($record['rate']) && $record['rate']) $record['rate_C'] = currency($record['rate'],$user['company']['language'],$user['company']['currency']);
			if (isset($record['totalCost']) && $record['totalCost']) $record['totalCost_C'] = currency($record['totalCost'],$user['company']['language'],$user['company']['currency']);
			if (isset($record['totalShouldbe']) && $record['totalShouldbe']) $record['totalShouldbe_C'] = currency($record['totalShouldbe'],$user['company']['language'],$user['company']['currency']);
			if (isset($record['InsertRate']) && $record['InsertRate']) $record['InsertRate_C'] = currency($record['InsertRate'],$user['company']['language'],$user['company']['currency']);
			$record['percent_diff'] = "";
			if ((isset($record['totalShouldbe']) && $record['totalShouldbe']) && (isset($record['totalCost']) && $record['totalCost'])) {
				$dif = $record['totalShouldbe'] - $record['totalCost'];
				if (($record['totalShouldbe'] != $record['totalCost']) && $record['totalShouldbe'] > 0) {
					$per = ($dif / $record['totalShouldbe']) * 100;
				} else {
					$per = 0;
				}
				$record['percent_diff'] = number_format($per, 2);
			}

			if (isset($record['datein']) && $record['datein']) $record['datein'] = datetime($record['datein'],'',$user['company']['timezone']);
			if (isset($record['dateChanged']) && $record['dateChanged']) $record['dateChanged'] = datetime($record['dateChanged'],'',$user['company']['timezone']);
			if (isset($record['checked_date']) && $record['checked_date']) $record['checked_date'] = datetime($record['checked_date'],'',$user['company']['timezone']);
			if (isset($record['material_date']) && $record['material_date']) $record['material_date'] = datetime($record['material_date'],'',$user['company']['timezone']);
			if (isset($record['last_change']) && $record['last_change']) $record['last_change'] = datetime($record['last_change'],'',$user['company']['timezone']);
			if (isset($record['deleted_date']) && $record['deleted_date']) $record['deleted_date'] = datetime($record['deleted_date'],'',$user['company']['timezone']);
			
			if (isset($record['cm']) && $record['cm']) $record['cm'] = $record['cm'] + 0;
			if (isset($record['totalspace']) && $record['totalspace']) $record['totalspace'] = $record['totalspace']+0;
			
			
		}

		return $record;
	}

	public static function display($data, $options = array("highlight" => "", "filter" => "*")) {
		$f3 = \Base::instance();
		if (!isset($options['highlight'])) $options['highlight'] = "";
		if (!isset($options['filter'])) $options['filter'] = "";
		$timer = new timer();
		$user = $f3->get("user");
		$permissions = $user['permissions'];
		if (is_array($data)) {
			$a = array();
			foreach ($data as $item) {
				if (isset($item['x_offset']) && $item['x_offset'])$item['x_offset'] = $item['x_offset'] + 0;
				if (isset($item['y_offset']) && $item['y_offset'])$item['y_offset'] = $item['y_offset'] + 0;

				
				

				$item = bookings::localization($item);
				$showrecord = true;
				$item['size'] = "";
				switch ($item['typeID']) {
					case 1:
						$item['size'] = $item['totalspace'] . "&nbsp;<span class='size'>" . $item['cm'] . "&nbsp;x&nbsp;" . $item['col'] . "</span>";
						break;
					case 2:
						$item['size'] = $item["InsertPO"];
						break;
				}
				if (isset($item['last_change'])) $item['last_change_date'] = date("Y-m-d", strtotime($item['last_change']));
				if (isset($item['pageID']) && $item['pageID'] && $item["page"]) {
					$item["page"] = number_format($item['page'], 0);
				}
				if (isset($item['material_file_filesize']) && $item['material_file_filesize']) {
					$item["material_file_filesize"] = file_size($item['material_file_filesize']);
				}
				if (($user['permissions']['view']['only_my_records'] == '1')) {
					if ($user['ID'] != $item['userID']) {
						$item['haha'] = $user['ID'];
						$showrecord = false;
					}
				}
				if ($showrecord) $a[] = $item;
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
			//	test_array($permissions);
//echo $record[$options["highlight"]] . " | " . $showrecord . " | " . $options["filter"]. "<br>";
			if ($showrecord) {
				if (!isset($a[$record['heading']])) {
					$groups[] = $record['heading'];
					$arr = array("heading" => $record['heading'], "count" => "", "cm" => 0, "percent" => "", "pages" => "",);
					$arr['totalCost'] = 0;
					$arr['groups'] = "";
					$arr['records'] = "";
					$a[$record['heading']] = $arr;
				}
				if ($record['typeID'] == '1') {
					$a[$record['heading']]["cm"] = $a[$record['heading']]["cm"] + $record['totalspace'];
				}
				$a[$record['heading']]["totalCost"] = $a[$record['heading']]["totalCost"] + $record['totalCost'];
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
			if (isset($permissions['lists']['totals']['totalCost']) && $permissions['lists']['totals']['totalCost']) {
				$record['totalCost'] = currency($record['totalCost'],$user['company']['language'],$user['company']['currency']);
			} else {
				if (isset($record['totalCost'])) unset($record['totalCost']);
			};
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
				$orderby = "if(typeID='1',COALESCE(COALESCE(if(ab_placing_sub.placingID=ab_bookings.placingID,system_publishing_colours_2.colourLabel,NULL), system_publishing_colours_1.colourLabel, system_publishing_colours.colourLabel),'zzzzzzzzz'),'zzzzzzzzz') $ordering, ab_bookings_types.orderby, " . $orderby;
				$arrange = "if(typeID='1',COALESCE(COALESCE(if(ab_placing_sub.placingID=ab_bookings.placingID,system_publishing_colours_2.colourLabel,NULL), system_publishing_colours_1.colourLabel, system_publishing_colours.colourLabel),''),ab_bookings_types.type) as heading";
				break;
			case "discountPercent":
				$orderby = "if((totalShouldbe<>totalCost) AND totalShouldbe>0,if(((totalShouldbe - totalCost))>0,if((totalShouldbe - totalCost)/totalShouldbe>0.5,5,if((totalShouldbe - totalCost)/totalShouldbe>0.2,4,3)),0),1) $ordering, " . $orderby;
				$arrange = "if((totalShouldbe<>totalCost) AND totalShouldbe>0,if(((totalShouldbe - totalCost))>0,if((totalShouldbe - totalCost)/totalShouldbe>0.5,'50%+ Under Charged',if((totalShouldbe - totalCost)/totalShouldbe>0.2,'20%+  Under Charged','Under Charged')),'Over Charged'),'No Discount') as heading";
				break;
			case "accountStatus":
				$orderby = "COALESCE(ab_accounts_status.orderby,99999) $ordering,  ab_bookings_types.orderby, " . $orderby;
				$arrange = "if(ab_accounts_status.status<>'',concat('Account - ',ab_accounts_status.status),ab_bookings_types.type) as heading";
				break;
			case "material_production":
				$orderby = "if(typeID='1',(CASE material_source WHEN 1 THEN 0 WHEN 2 THEN 1 END),99999) $ordering, ab_bookings_types.orderby, ab_production.production $ordering,  " . $orderby;
				$arrange = "if(typeID='1',COALESCE((CASE material_source WHEN 1 THEN ab_production.production WHEN 2 THEN 'Supplied' END),'None'),ab_bookings_types.type) as heading";
				break;
			case "invoicedStatus":
				$orderby = "if (invoiceNum,1,0) $ordering, " . $orderby;
				$arrange = "if (invoiceNum,'Invoiced','Not Invoiced') as heading";
				break;
			case "payment_method":
				$orderby = "COALESCE(system_payment_methods.label,'zzzzzzzzz') $ordering, " . $orderby;
				$arrange = "COALESCE(system_payment_methods.label,'None') as heading";
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
		$a = new \DB\SQL\Mapper($f3->get("DB"), "ab_bookings");
		$a->load("ID='$ID'");
		if (!$a->dry()) {
			$a->deleted = "1";
			$a->deleted_userID = $userID;
			$a->deleted_user = $user['fullName'];
			$a->deleted_date = date("Y-m-d H:i:s");
			$a->deleted_reason = ($reason);
			$a->dateChanged = date("Y-m-d H:i:s");
			$a->save();
			$changes = array(array("k" => "Deleted", "v" => "1", "w" => ""), array("k" => "deleted_user", "v" => $user['fullName'], "w" => ""), array("k" => "deleted_reason", "v" => $reason, "w" => ""));
			bookings::logging($a->ID, $changes, "Booking Deleted");
		}
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args()
		);

		return "deleted";
	}

	public static function repeat($ID = "", $dID, $exact_repeat = '1') {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		$dataO = new bookings();
		$data = $dataO->get($ID);
		if (!$data['ID'] || $data["accountBlocked"] == '1') {
			exit();
		}
		$values = $data;
		unset($values['ID']);
		unset($values['logs']);
		unset($values['publishDate']);
		unset($values['dID']);
		unset($values['userName']);
		$values['userID'] = $user['ID'];
		$values['checked'] = "0";
		$values['checked_date'] = null;
		$values['checked_userID'] = null;
		$values['checked_user'] = null;
		$values['userName'] = $user['fullName'];
		$values['repeat_from'] = $data['ID'];
		$values['pageID'] = null;
		$values['invoiceNum'] = null;
		$values['dID'] = $dID;
		if ($exact_repeat) {
			$label1 = "Booking was repeated";
			$label2 = "Repeat Booking";
		} else {
			$label1 = "Booking was repeated (material not kept)";
			$label2 = "Repeat Booking (material not kept)";
			unset($values['material_file_filename']);
			unset($values['material_file_filesize']);
			unset($values['material_file_store']);
			unset($values['material_status']);
			unset($values['material_date']);
			unset($values['material_approved']);
			unset($values['orderNum']);
			unset($values['keyNum']);
		}
		$a = new \DB\SQL\Mapper($f3->get("DB"), "ab_bookings");
		foreach ($values as $key => $value) {
			if (isset($a->$key)) {
				$a->$key = $value;
			}
		}
		$a->save();
		$ID = $a->ID;
		$n = $dataO->get($ID);
		$log = array(array("k" => "Repeated", "v" => $ID, "w" => $data['ID']), array("k" => "Date", "v" => $n['publishDate'], "w" => $data['publishDate']));
		$cfg = $f3->get("CFG");
		$cfg = $cfg['upload'];
		$cID = $data['cID'];
		if ($exact_repeat) {
			$oldFolder = $cfg['folder'] . "ab/" . $cID . "/" . $data['pID'] . "/" . $data['dID'] . "/material/";
			$newFolder = $cfg['folder'] . "ab/" . $cID . "/" . $data['pID'] . "/" . $values['dID'] . "/material/";
			if (file_exists($oldFolder . $data['material_file_store'])) {
				if (!file_exists($newFolder)) @mkdir($newFolder, 0777, true);
				@copy($oldFolder . $data['material_file_store'], $newFolder . $data['material_file_store']);
			}
		} else {
		}
		//	test_array(array("o"=>$oldFolder,"n"=>$newFolder));
		bookings::logging($data['ID'], $log, $label1);
		bookings::logging($ID, $log, $label2);
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args()
		);

		return $n;
	}

	public static function save($ID = "", $values = array(), $opts = array("dry" => true, "section" => "booking")) {
		//test_array($values);
		$timer = new timer();
		$f3 = \Base::instance();
		$lookupColumns = array();
		$lookupColumns["dID"] = array("sql" => "(SELECT publish_date FROM global_dates WHERE ID = '{val}')", "col" => "publish_date", "val" => "");
		$lookupColumns["placingID"] = array("sql" => "(SELECT placing FROM ab_placing WHERE ID = '{val}')", "col" => "placing", "val" => "");
		$lookupColumns["categoryID"] = array("sql" => "(SELECT `category` FROM ab_categories WHERE ID = '{val}')", "col" => "category", "val" => "");
		$lookupColumns["marketerID"] = array("sql" => "(SELECT `marketer` FROM ab_marketers WHERE ID = '{val}')", "col" => "marketer", "val" => "");
		$lookupColumns["sub_placingID"] = array("sql" => "(SELECT `label` FROM ab_placing_sub WHERE ID = '{val}')", "col" => "sub_placing", "val" => "");
		$lookupColumns["colourID"] = array("sql" => "(SELECT `colourLabel` FROM system_publishing_colours WHERE ID = '{val}')", "col" => "colour", "val" => "");
		$lookupColumns["material_productionID"] = array("sql" => "(SELECT `production` FROM ab_production WHERE ID = '{val}')", "col" => "production", "val" => "");
		$lookupColumns["remarkTypeID"] = array("sql" => "(SELECT `remarkType` FROM ab_remark_types WHERE ID = '{val}')", "col" => "remarkType", "val" => "");
		$lookupColumns["checked_userID"] = array("sql" => "(SELECT `fullName` FROM global_users WHERE ID = '{val}')", "col" => "checked_user", "val" => "");
		$lookupColumns["deleted_userID"] = array("sql" => "(SELECT `fullName` FROM global_users WHERE ID = '{val}')", "col" => "deleted_user", "val" => "");
		$lookupColumns["material_source"] = array("sql" => "(CASE '{val}' WHEN 1 THEN 'Production' WHEN 2 THEN 'Supplied' END)", "col" => "material_source", "val" => "");
		$lookupColumns["material_status"] = array("sql" => "(CASE '{val}' WHEN 1 THEN 'Ready' WHEN 0 THEN 'Not Ready' END)", "col" => "material_status", "val" => "");
		$lookupColumns["checked"] = array("sql" => "(CASE '{val}' WHEN 1 THEN 'Checked' WHEN 0 THEN 'Not Checked' END)", "col" => "checked", "val" => "");
		$lookupColumns["pageID"] = array("sql" => "(SELECT TRUNCATE(`page`,0) FROM global_pages WHERE ID = '{val}')", "col" => "page", "val" => "");
		$lookupColumns["accountID"] = array("sql" => "(SELECT concat(accNum,' | ',account) FROM ab_accounts WHERE ID = '{val}')", "col" => "Account", "val" => "");
		$lookupColumns["payment_methodID"] = array("sql" => "(SELECT label FROM system_payment_methods WHERE ID = '{val}')", "col" => "payment_method", "val" => "");
		$lookup = array();
		$a = new \DB\SQL\Mapper($f3->get("DB"), "ab_bookings");
		$a->load("ID='$ID'");
		$cfg = $f3->get("CFG");
		$cfg = $cfg['upload'];
		//test_array($cfg);
		$user = $f3->get("user");
		$cID = $user['company']['ID'];
		if (($cfg['material'] && $user['company']['ab_upload_material'] == '1' && $user['publication']['ab_upload_material'] == '1') && !$a->dry()) {
			if ($a->material_file_store) {
				$oldFolder = $cfg['folder'] . "ab/" . $cID . "/" . $a->pID . "/" . $a->dID . "/material/";
				if ((isset($values['material_status']) && $values['material_status'] == "0" && $a->material_file_store) || (isset($values['material_file_store']) && $a->material_file_store != $values['material_file_store'])) {
					if (file_exists($oldFolder . $a->material_file_store)) {
						@unlink($oldFolder . $a->material_file_store);
					}
				} else {
					if (isset($values['dID'])) {
						//echo "old: " . $oldFolder . $a->material_file_store . "<br>";
						if (file_exists($oldFolder . $a->material_file_store)) {
							$newFolder = $cfg['folder'] . "ab/" . $cID . "/" . $a->pID . "/" . $values['dID'] . "/material/";
							//echo "new: ". $newFolder . $a->material_file_store ."<br>";
							if (!file_exists($newFolder)) @mkdir($newFolder, 0777, true);
							@rename($oldFolder . $a->material_file_store, $newFolder . $a->material_file_store);
						}
					}
				}
			}
		}
		$changes = array();
		$material = false;
		foreach ($values as $key => $value) {
			if (strpos($key, "aterial_")) $material = true;
			if (isset($a->$key)) {
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
			$label = "Booking Added";
			$ID = $a->ID;
		} else {
			$label = "Booking Edited";
		}
		$sql = "SELECT 1 ";
		if ($material) {
			$sql .= ", (SELECT `production` FROM ab_production WHERE ID = '" . $a->material_productionID . "') AS production";
		}
		foreach ($lookup as $col) {
			$sql .= ", " . str_replace("{val}", $col['val'], $col['sql']) . " AS " . $col['col'];
			$sql .= ", " . str_replace("{val}", $col['was'], $col['sql']) . " AS " . $col['col'] . "_was";
		}
		$v = $f3->get("DB")->exec($sql);
		$v = $v[0];
		foreach ($lookup as $col) {
			$changes[] = array("k" => $col['col'], "v" => $v[$col['col']], "w" => $v[$col['col'] . "_was"]);
		}
		if (isset($opts['section']) && $opts['section']) {
			switch ($opts['section']) {
				case "material":
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
				case "material_approved":
					if ($a->material_approved == '1') {
						$label = "Material Approved";
					} else {
						$label = "Material Not Approved";
					}
					break;
				case "checked":
					if ($a->checked == '1') {
						$label = "Booking Checked";
					} else {
						$label = "Booking Not Checked";
					}
					break;
				case "invoice":
					if ($a->invoiceNum) {
						$label = "Booking Invoiced";
					} else {
						$label = "Booking Not Invoiced";
					}
					break;
				case "layout":
					if ($a->pageID) {
						$label = "Booking added to a page";
					} else {
						$label = "Booking removed from a page";
					}
					break;
			}
		}
		if (count($changes)) bookings::logging($ID, $changes, $label);
		$n = new bookings();
		$n = $n->get($ID);
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args()
		);

		return $n;
	}

	private static function getLogs($ID) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		$return = $f3->get("DB")->exec("SELECT *, (SELECT fullName FROM global_users WHERE global_users.ID =ab_bookings_logs.userID ) AS fullName FROM ab_bookings_logs WHERE bID = '$ID' ORDER BY datein DESC");
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
		$f3->get("DB")->exec("INSERT INTO ab_bookings_logs (`bID`, `log`, `label`, `userID`) VALUES ('$ID','$log','$label','$userID')");
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args()
		);
	}

	public static function dbStructure() {
		$f3 = \Base::instance();
		$table = $f3->get("DB")->exec("EXPLAIN ab_bookings;");
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