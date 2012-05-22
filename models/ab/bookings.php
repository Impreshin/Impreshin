<?php

namespace models\ab;
use \F3 as F3;
use \Axon as Axon;
use \timer as timer;
class bookings {
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
			SELECT ab_bookings.*,
				ab_placing.placing AS placing,
				ab_bookings_types.type AS type,
				ab_marketers.marketer AS marketer,
				global_publications.publication AS publication,
				global_dates.publish_date AS publish_date,
				ab_categories.category AS category,
				global_users.fullName AS byFullName,
				ab_accounts.account AS account,
				ab_accounts_status.blocked AS accountBlocked, ab_accounts_status.labelClass,ab_accounts_status.status AS accountStatus,
				ab_accounts.accNum AS accNum,
				ab_remark_types.remarkType, ab_remark_types.labelClass AS remarkTypeLabelClass
			FROM (((((((((ab_bookings LEFT JOIN ab_placing ON ab_bookings.placingID = ab_placing.ID) LEFT JOIN ab_bookings_types ON ab_bookings.typeID = ab_bookings_types.ID) LEFT JOIN ab_marketers ON ab_bookings.marketerID = ab_marketers.ID) LEFT JOIN ab_categories ON ab_bookings.categoryID = ab_categories.ID) LEFT JOIN global_users ON ab_bookings.userID = global_users.ID) LEFT JOIN global_publications ON ab_bookings.pID = global_publications.ID) INNER JOIN ab_accounts ON ab_bookings.accNum = ab_accounts.accNum) LEFT JOIN global_dates ON ab_bookings.dID = global_dates.ID) INNER JOIN ab_accounts_status ON ab_accounts.statusID = ab_accounts_status.ID) INNER JOIN ab_remark_types ON ab_bookings.remarkTypeID = ab_remark_types.ID
			WHERE ab_bookings.ID = '$ID';

		"
		);


		if (count($result)) {
			$return = bookings::currency($result[0]);
			$return['publishDateDisplay'] = date("d F Y", strtotime($return['publishDate']));

		} else {
			$return = $this->dbStructure;
		}
		$timer->stop("Models - bookings - get", func_get_args());
		return $return;
	}
	public static function getAll($where = "", $grouping = "none", $ordering = "ASC") {
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



		$result = F3::get("DB")->exec("
			SELECT ab_bookings.*, ab_placing.placing, ab_bookings_types.type, ab_marketers.marketer,
				ab_accounts.account AS account, ab_accounts_status.blocked AS accountBlocked, ab_accounts_status.status AS accountStatus, ab_accounts_status.labelClass,
				ab_remark_types.remarkType, ab_remark_types.labelClass AS remarkTypeLabelClass,
				0 as material,
				0 as layout
			$select
			FROM (((((ab_bookings LEFT JOIN ab_placing ON ab_bookings.placingID = ab_placing.ID) LEFT JOIN ab_bookings_types ON ab_bookings.typeID = ab_bookings_types.ID) LEFT JOIN ab_marketers ON ab_bookings.marketerID = ab_marketers.ID) LEFT JOIN ab_accounts ON ab_bookings.accNum = ab_accounts.accNum) INNER JOIN ab_accounts_status ON ab_accounts.statusID = ab_accounts_status.ID)  INNER JOIN ab_remark_types ON ab_bookings.remarkTypeID = ab_remark_types.ID

			$where
			$orderby
		");


		$return = $result;
		$timer->stop("Models - bookings - getAll", func_get_args());
		return $return;
	}

	public static function stats($where){
		$timer = new timer();

		if (is_array($where)){
			$data = $where;
		} else {
			$data = bookings::getAll("",$where);
		}
		$totals = array(
			"records"=> count($data),
			"cm"=>0,
			"checked"=>0,
			"material"=>0,
			"layout"=>0,

		);

		foreach ($data as $record){
			if ($record['totalspace']) $totals['cm'] = $totals['cm'] + $record['totalspace'];
			if ($record['checked']) $totals['checked'] = $totals['checked'] + 1;
			if ($record['material']) $totals['material'] = $totals['material'] + 1;
			if ($record['layout']) $totals['layout'] = $totals['layout'] + 1;

		}



		$return = array(
			"cm"=> $totals['cm'],
			"records"=> array(
				"total"=>$totals["records"],
				"checked"=> array(
					"r"=>$totals["checked"],
					"p"=> ($totals['records']) ?number_format((($totals["checked"] / $totals["records"]) * 100), 2):0
				),
				"material"=> array(
					"r"=> $totals["material"],
					"p"=> ($totals['records']) ?number_format((($totals["material"] / $totals["records"]) * 100), 2):0
				),
				"layout"=> array(
					"r"=> $totals["layout"],
					"p"=> ($totals['records'])?number_format((($totals["layout"] / $totals["records"]) * 100), 2):0
				),
			),


		);


		$timer->stop("Models - bookings - stats");
		return $return;
	}
	private static function currency($record){
		if (is_array($record)){
			if (isset($record['colourCost']) && $record['colourCost']) $record['colourCost_C'] = currency($record['colourCost']);
			if (isset($record['rate']) && $record['rate']) $record['rate_C'] = currency($record['rate']);
			if (isset($record['totalCost']) && $record['totalCost']) $record['totalCost_C'] = currency($record['totalCost']);
			if (isset($record['totalShouldbe']) && $record['totalShouldbe']) $record['totalShouldbe_C'] = currency($record['totalShouldbe']);
			if (isset($record['InsertRate']) && $record['InsertRate']) $record['InsertRate_C'] = currency($record['InsertRate']);


			$record['percent_diff'] ="";
			if ((isset($record['totalShouldbe']) && $record['totalShouldbe']) && (isset($record['totalCost']) && $record['totalCost'])){
				$dif = $record['totalShouldbe'] - $record['totalCost'];
				if (($record['totalShouldbe']!= $record['totalCost']) && $record['totalShouldbe'] > 0) {
					$per = ($dif / $record['totalShouldbe']) * 100;
				} else {
					$per = 0;
				}


				$record['percent_diff'] = number_format($per,2);
			}

		}
		return $record;

	}
	public static function display($data, $options=array()){
		if (!isset($options['highlight']))$options['highlight']="";
		if (!isset($options['filter']))$options['filter']="";

		$timer = new timer();
		if (is_array($data)){
			$a = array();
			foreach ($data as $item){

				$item['size'] = "";
				switch ($item['typeID']) {
					case 1:
						$item['size'] = $item['totalspace'] . "<span class='size'>". $item['col'] . " x " . $item['cm'] . "</span>";
						break;
					case 2:
						$item['size'] = $item["InsertPO"];
						break;

				}



				$a[] = bookings::currency($item);
			}
			$data = $a;

		}



		$return = array();
		$a = array();
		$groups = array();
		foreach ($data as $record) {
			if ($options["highlight"]) {
				$record['highlight'] = $record[$options["highlight"]];
			}
			$showrecord = true;

			if ($options["filter"]=="*"){
				$showrecord = true;
			} else {
				if ($record[$options["highlight"]] == $options['filter'] ){
					$showrecord = true;
				} else {
					$showrecord = false;
				}

			}

//echo $record[$options["highlight"]] . " | " . $showrecord . " | " . $options["filter"]. "<br>";
			if ($showrecord){
				if (!isset($a[$record['heading']])) {
					$groups[] = $record['heading'];
					$a[$record['heading']] = array(
						"heading" => $record['heading'],
						"count"   => "",
						"cm"      => 0,
						"percent" => "",
						"pages"   => "",
						"groups"  => "",
						"records" => ""
					);
				}
				if ($record['typeID'] == '1') {
					$a[$record['heading']]["cm"] = $a[$record['heading']]["cm"] + $record['totalspace'];
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
		$timer->stop("Models - bookings - display");
		return $return;

	}
	private static function order($grouping, $ordering){
		$orderby = " client ASC";
		$arrange = "";
		switch ($grouping) {
			case "type":
				$orderby = "COALESCE(ab_bookings_types.orderby,99999) $ordering, " . $orderby;
				$arrange = "COALESCE(ab_bookings_types.type,ab_bookings_types.type) as heading";
				break;
			case "placing":
				$orderby = "COALESCE(ab_placing.orderby,99999) $ordering,  ab_bookings_types.orderby," . $orderby;
				$arrange = "COALESCE(ab_placing.placing,ab_bookings_types.type) as heading";
				break;
			case "marketer":
				$orderby = "COALESCE(ab_marketers.marketer,99999) $ordering, " . $orderby;
				$arrange = "COALESCE(ab_marketers.marketer,'None') as heading";
				break;
			case "columns":
				$orderby = "if(typeID='1',col,99999) $ordering, ab_bookings_types.orderby, " . $orderby;
				$arrange = "if(typeID='1',col,ab_bookings_types.type) as heading";
				break;
			case "colours":
				$orderby = "if(typeID='1',colour,99999) $ordering, ab_bookings_types.orderby, " . $orderby;
				$arrange = "if(typeID='1',colour,ab_bookings_types.type) as heading";
				break;
			case "discountPercent":
				$orderby = "if((totalShouldbe<>totalCost) AND totalShouldbe>0,if(((totalShouldbe - totalCost))>0,1,2),0) $ordering, " . $orderby;
				$arrange = "if((totalShouldbe<>totalCost) AND totalShouldbe>0,if(((totalShouldbe - totalCost))>0,'Under Charged','Over Charged'),'No Discount') as heading";
				break;
			case "accountStatus":
				$orderby = "COALESCE(ab_accounts_status.orderby,99999) $ordering,  ab_bookings_types.orderby, " . $orderby;
				$arrange = "if(ab_accounts_status.status<>'',concat('Account - ',ab_accounts_status.status),ab_bookings_types.type) as heading";
				break;
			case "none":
				$orderby = "" . $orderby;
				$arrange = "'None' as heading";
				break;

		}

		return array(
			"order"=> $orderby,
			"select"=> $arrange
		);
	}

	public static function save($ID="",$values=array()){
		$timer = new timer();

		$a = new Axon("ab_bookings");
		$a->load("ID='$ID'");

		$t = array();


		foreach ($values as $key=>$value){
			$a->$key = $value;
		}

		$a->save();
		if (!$ID){
			$action = "Add";
			$ID = $a->_id;
		} else {
			$action = "Edit";
		}



		$n = new bookings();
		$n = $n->get($ID);

		$n['action'] = $action;

		$timer->stop("Models - bookings - save");
		return $n;
	}

	private static function dbStructure() {
		$table = F3::get("DB")->exec("EXPLAIN ab_bookings;");
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