<?php
/*
 * Date: 2011/12/08
 * Time: 4:30 PM
 */
namespace models;
use \F3 as F3;
use \Axon as Axon;
use \timer as timer;
class logging {
	private $log = array();
	private $companyID;
	private $meetingID;

	function __construct() {

	}

	public static function getAll($where="",$orderby=""){
		$timer = new timer();

		if ($where) {
			$where = "WHERE " . $where;
		}
		if ($orderby) {
			$orderby = "ORDER BY " . $orderby;
		}

		$result = F3::get("DB")->sql("SELECT *, (SELECT fullName FROM global_users WHERE global_users.ID =global_logs.uID ) AS fullName FROM global_logs $where $orderby");

		$a = array();
		foreach ($result as $record) {
			$record['logs'] = json_decode($record['log']);
			$a[] = $record;
		}


		$return = $a;


		$timer->stop(array("Models"=> array("Class" => __CLASS__,"Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}


	public static function save($section, $changes=array(), $label = ""){
		$return = "";
		$user = F3::get("user");
		$userID = $user['ID'];
		$app = F3::get("app");

		//$section = str_replace("models".$app,"",$section);


		//test_array($changes);
		if (count($changes)) {
			$log = mysql_escape_string(json_encode($changes));

			F3::get("DB")->exec("INSERT INTO global_logs (`app`,`section`, `log`, `label`, `uID`) VALUES ('$app','$section','$log','$label','$userID')");
		}







		return $changes;

	}
	public static function _log($class, $label, $values,$old, $overwrite = array(), $lookups=array()){
		$changes = array();
		$lookup = array();


		$t = array();
		foreach ($overwrite as $k=>$v){
			if (!$k) $k = $v;
			$t[] = $k;
		}







		foreach ($values as $key=> $value) {
			if (!in_array($key, $t)) {
				$cur = $old[$key];
				if (($cur != $value)) {
					if (isset($lookups[$key])) {
						$lookups[$key]['val'] = $value;
						$lookups[$key]['was'] = $cur;
						$lookup[] = $lookups[$key];
					} else {
						$w = $cur;
						$v = $value;
						$changes[] = array(
							"k"=> $key,
							"v"=> $v,
							"w"=> str_replace("0000-00-00 00:00:00", "", $w)
						);
					}

				}
			}
		}

		foreach ($overwrite as $k=> $v) {
			if (is_array($overwrite) && count($overwrite)) {
				if (is_array($v)) $changes[] = $v;
			}
		}

		$sql = "SELECT 1 ";

		foreach ($lookup as $col) {
			$sql .= ", " . str_replace("{val}", $col['val'], $col['sql']) . " AS " . $col['col'];
			$sql .= ", " . str_replace("{val}", $col['was'], $col['sql']) . " AS " . $col['col'] . "_was";
		}

//test_array($sql);
		$v = F3::get("DB")->exec($sql);
		$v = $v[0];

//		test_array($v);
		foreach ($lookup as $col) {
			$changes[] = array(
				"k"=> $col['col'],
				"v"=> $v[$col['col']],
				"w"=> $v[$col['col'] . "_was"]
			);
		}

		//test_array($changes);

		self::save($class,$changes,$label);

	}


}
