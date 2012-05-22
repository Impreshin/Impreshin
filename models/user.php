<?php
/*
 * Date: 2011/11/16
 * Time: 11:29 AM
 */
namespace models;
use \F3 as F3;
use \Axon as Axon;
use \timer as timer;
class user {
	public $ID;
	private $dbStructure;

	function __construct() {
		$classname = get_class($this);
		$this->dbStructure = $classname::dbStructure();

	}



	public function get($ID = "") {
		$timer = new timer();

		$ab_defaults = F3::get("ab_defaults");

		$result = F3::get("DB")->exec("
			SELECT global_users.*,
				ab_users_settings.settings,
				ab_users_settings.pID as ab_pID
			FROM `global_users` LEFT JOIN ab_users_settings ON global_users.ID = ab_users_settings.uID
			WHERE global_users.ID = '$ID'
		"
		);
		//echo $ID . "<br>";
		//print_r($result);echo "<hr>";

		if (count($result)){
			$result = $result[0];






			$result['settings']= array_replace_recursive((array)$ab_defaults, (array)($result['settings'])?unserialize($result['settings']):array());;

			//test_array($result['settings']);
			$result['settings']['list']['count'] = count($result['settings']['list']['col']);
			$av_publications = ab\publications::getAll("uID='".$result['ID']."'");
			$ab_pID = $av_publications[0]['ID'];


			$pubstr = array();
			foreach ($av_publications AS $pub) $pubstr[] = $pub["ID"];

			if (in_array($result['ab_pID'], $pubstr)) {
				$ab_pID = $result['ab_pID'];
			}
			$result['ab_pID'] = $ab_pID;


		} else {
			$result = $this->dbStructure();
		}
		$return = $result;
		$timer->stop("Models - user - get", func_get_args());
		return $return;
	}
	public static function save_setting($values = array(), $app = "ab",$uID=""){
		$timer = new timer();
		if (!$uID){
			$user = F3::get("user");
			$uID = $user['ID'];
		}




		$t = New Axon("ab_users_settings");
		$t->load("uID='$uID'");

		$t->uID=$uID;

		$v = array_replace_recursive(($t->settings) ? unserialize($t->settings) : array(), $values);
		if (isset($values['list']['col'])) $v['list']['col'] = $values['list']['col'];
		//test_array($v);

		if(count($values)) $t->settings = serialize($v);

		$t->save();



		$timer->stop("Models - user - save_setting", func_get_args());
		return "done";
	}

	public static function save_config($values = array(), $app = "ab", $uID = "") {
		$timer = new timer();
		if (!$uID) {
			$user = F3::get("user");
			$uID = $user['ID'];
		}


		$t = New Axon("ab_users_settings");
		$t->load("uID='$uID'");

		$t->uID = $uID;

		if (isset($values['pID'])) $t->pID = $values['pID'];


		$t->save();


		$timer->stop("Models - user - save_config", func_get_args());
		return $uID;
	}

	private static function dbStructure() {
		$table = F3::get("DB")->exec("EXPLAIN global_users;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}

		return $result;
	}
}
