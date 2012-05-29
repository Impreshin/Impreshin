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
				ab_remark_types.remarkType, ab_remark_types.labelClass AS remarkTypeLabelClass,
				(SELECT production FROM ab_production WHERE ab_production.ID = ab_bookings.material_productionID ) AS material_production
			FROM (((((((((ab_bookings LEFT JOIN ab_placing ON ab_bookings.placingID = ab_placing.ID) LEFT JOIN ab_bookings_types ON ab_bookings.typeID = ab_bookings_types.ID) LEFT JOIN ab_marketers ON ab_bookings.marketerID = ab_marketers.ID) LEFT JOIN ab_categories ON ab_bookings.categoryID = ab_categories.ID) LEFT JOIN global_users ON ab_bookings.userID = global_users.ID) LEFT JOIN global_publications ON ab_bookings.pID = global_publications.ID) INNER JOIN ab_accounts ON ab_bookings.accNum = ab_accounts.accNum) LEFT JOIN global_dates ON ab_bookings.dID = global_dates.ID) INNER JOIN ab_accounts_status ON ab_accounts.statusID = ab_accounts_status.ID) INNER JOIN ab_remark_types ON ab_bookings.remarkTypeID = ab_remark_types.ID
			WHERE ab_bookings.ID = '$ID';

		"
		);


		if (count($result)) {
			$return = bookings::currency($result[0]);
			$return['publishDateDisplay'] = date("d F Y", strtotime($return['publishDate']));
			$return['logs'] = bookings::getLogs($return['ID']);

		} else {
			$return = $this->dbStructure;
		}
		$timer->stop("Models - bookings - get", func_get_args());
		return $return;
	}
	public static function getAll($where = "", $grouping = array("g"=>"none","o"=>"ASC"), $ordering = array("c"=>"client","o"=>"ASC")) {
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
				material_status as material,
				CASE material_source WHEN 1 THEN 'Production' WHEN 2 THEN 'Supplied' END AS material_source,
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

		$o = explode(".", $ordering['c']);
		$a = array();
		foreach($o as $b) {
			$a[] = "`" . $b . "`";
		}
		$a = implode(".",$a);
		$orderby = " ". $a . " " . $ordering['o'];
		$arrange = "";
		$ordering = $grouping['o'];
		switch ($grouping['g']) {
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

			case "material_production":
				$orderby = "if(typeID='1',(CASE material_source WHEN 1 THEN 0 WHEN 2 THEN 1 END),99999) $ordering, ab_bookings_types.orderby, ab_bookings.material_production $ordering,  " . $orderby;
				$arrange = "if(typeID='1',COALESCE((CASE material_source WHEN 1 THEN ab_bookings.material_production WHEN 2 THEN 'Supplied' END),'None'),ab_bookings_types.type) as heading";
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

	public static function save($ID="",$values=array(),$opts=array("dry"=>true,"section"=>"booking")){
		$timer = new timer();
		$lookupColumns = array();
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

		$lookup = array();



		$a = new Axon("ab_bookings");
		$a->load("ID='$ID'");


		$changes = array();
		$material = false;
		foreach ($values as $key=>$value){
			if (strpos($key,"aterial_")) $material = true;

			$cur = $a->$key;
			if ($cur != $value) {
				if (isset($lookupColumns[$key])){
					$lookupColumns[$key]['val']=$value;
					$lookupColumns[$key]['was']= $cur;
					$lookup[] = $lookupColumns[$key];
				} else {
					$changes[] = array(
						"k"=> $key,
						"v"=> $value,
						"w"=> str_replace("0000-00-00 00:00:00","",$cur)
					);
				}

			}
			$a->$key = $value;
		}

		if ($opts['dry'] || !$a->dry()){
			$a->save();
		}


		if (!$ID){
			$label = "Booking Added";
			$ID = $a->_id;
		} else {
			$label = "Booking Edited";
		}

		$sql = "SELECT 1 ";
		if ($material) {
			$sql .= ", (SELECT `production` FROM ab_production WHERE ID = '" . $a->material_productionID . "') AS production";
		}
		foreach ($lookup as $col){
			$sql .= ", ". str_replace("{val}",$col['val'],$col['sql']) ." AS ".$col['col'];
			$sql .= ", ". str_replace("{val}",$col['was'],$col['sql']) ." AS ".$col['col']."_was";
		}



		$v = F3::get("DB")->exec($sql);
		$v = $v[0];
		foreach ($lookup as $col) {
			$changes[] = array(
				"k"=> $col['col'],
				"v"=> $v[$col['col']],
				"w"=> $v[$col['col'] . "_was"]
			);
		}


//test_array($v);

		if (isset($opts['section']) && $opts['section']){
			switch ($opts['section']){
				case "material":
					if ($a->material_status == '1') {
						$label = "Material - Ready";
						if ($a->material_source == '1') {
							$production = (isset($v['production']))? $v['production']:"";
							if ($production) $label .= " (".$production.")";
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
			}
		}





		if (count($changes)) bookings::logging($ID,$changes, $label);


		$n = new bookings();
		$n = $n->get($ID);



		$timer->stop("Models - bookings - save");
		return $n;
	}

	private static function getLogs($ID) {
		$timer = new timer();

		$return = F3::get("DB")->exec("SELECT *, (SELECT fullName FROM global_users WHERE global_users.ID =ab_bookings_logs.userID ) AS fullName FROM ab_bookings_logs WHERE bID = '$ID' ORDER BY datein DESC");
		$a = array();
		foreach ($return as $record){
			$record['log'] = json_decode($record['log']);
			$a[] = $record;
		}

		$timer->stop("Models - bookings - getLogs");
		return $a;
	}

	private static function logging($ID,$log=array(),$label="Log"){
		$timer = new timer();
		$user = F3::get("user");
		$userID = $user['ID'];


		$log = json_encode($log);


		F3::get("DB")->exec("INSERT INTO ab_bookings_logs (`bID`, `log`, `label`, `userID`) VALUES ('$ID','$log','$label','$userID')");

		$timer->stop("Models - bookings - logging");
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