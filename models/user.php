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
		$result = F3::get("DB")->exec("
				SELECT global_users.*
				FROM global_users
				WHERE global_users.ID = '$ID'
			");
		if (count($result)) {
			$result = $result[0];
		} else {
			$result = $this->dbStructure();
		}

		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $result;
	}

	public function user($user = "") {
		$timer = new timer();
		$app = F3::get("app");
		$result = array();
		if (!is_array($user)){

			$user = $this->get($user);
		}


		$result = $user;

		if ($app && $user['ID']){

			$appClass = "\\models\\" . $app . "\\user_settings";

			$appO = new $appClass();
			$appSettings = $appO->_read($result['ID']);
			if ((isset($_GET['apID']) && $_GET['apID']) && $_GET['apID'] != $appSettings['pID']) {
				$appClass::save_config(array("pID"=> $_GET['apID']), $result['ID']);
				$appSettings = $appO->_read($result['ID']);
			}

			$appPublications = "\\models\\" . $app . "\\publications";
			$publications = $appPublications::getAll_user("uID='" . $result['ID'] . "'", "publication ASC");
			$pID = (count($publications)) ? $publications[0]['ID'] : "";


			$pubstr = array();
			$publication = "";
			foreach ($publications AS $pub) $pubstr[] = $pub["ID"];

			if (in_array($appSettings['pID'], $pubstr)) {
				$pID = $appSettings['pID'];

			}



			$publication = new $appPublications();
			$publication = $publication->get($pID);

			$result['pID'] = $pID;
			$result['publications'] = $publications;
			$result['publication'] = $publication;



			$extra = F3::get("DB")->exec("SELECT * FROM global_users_company WHERE uID='".$user['ID']."' AND cID='". $result['publication']['cID']."'");

			if (count($extra)){
				$extra = $extra[0];
			}

			if (!$extra[$app]) {
				F3::reroute("/noaccess/?app=$app");
			}


			$appClass = "\\models\\" . $app . "\\user_permissions";
			$permissions = $appClass::_read($extra[$app . '_permissions']);

			$permissions['records']['_nav'] = '0';
			foreach ($permissions['records'] as $p){
				if ($p['page']) $permissions['records']['_nav'] = '1';
			}

			$permissions['administration']['_nav'] = '0';
			foreach ($permissions['administration']['application'] as $p){
				if ($p['page']) $permissions['administration']['_nav'] = '1';
			}
			foreach ($permissions['administration']['system'] as $p){
				if ($p['page']) $permissions['administration']['_nav'] = '1';
			}

			$result['permissions'] = $permissions;

			unset($result['password']);
			//unset($result[$app . '_permissions']);

			if ($app=="ab"){
				$marketer = \models\ab\marketers_targets::_current($result['ID'],$result['publication']['ID']);
				if (isset($marketer['ID']) && $marketer['ID']){
					$result['marketer'] = $marketer;
				}
			}



			} else {
				$result['settings'] = array();
				$result['pID'] = "";
				$result['publications'] = array();
				$result['publication'] = array();
				$result['permissions'] = array();

			}









		$return = $result;
		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}
	function login($username,$password){
		$timer = new timer();

		$ID = "";

		if (isset($_COOKIE['username'])){
			$_COOKIE['username'] = $username;
		} else {
			setcookie("username", $username, time() + 31536000, "/");
		}



		$result = F3::get("DB")->exec("
			SELECT ID, email FROM global_users WHERE email ='$username' AND password = '$password'
		");


		if (count($result)){
			$result = $result[0];
			$ID = $result['ID'];
			$_SESSION['uID']=$ID;
			if (isset($_COOKIE['username'])) {
				$_COOKIE['username'] = $result['email'];
			} else {
				setcookie("username", $result['email'], time() + 31536000, "/");
			}
		}

		$return = $ID;
		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}
	static function getAll($where="",$orderby="fullName ASC",$limit=""){
		$timer = new timer();
		$user = F3::get("user");
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
		$apps = F3::get("cfg");
		$apps = $apps['apps'];

		$apps_str = "";
		foreach ($apps as $app){
			$apps_str .= "global_users_company.". $app.", (SELECT last_activity FROM ".$app."_users_settings WHERE " . $app . "_users_settings.uID = global_users.ID) as " . $app . "_last_activity,  if ((SELECT count(ID) FROM " . $app . "_users_pub WHERE " . $app . "_users_pub.uID = global_users.ID AND " . $app . "_users_pub.pID = '$pID' LIMIT 0,1)<>0,1,0) as currentPub, ";
		}


		$result = F3::get("DB")->exec("
			SELECT global_users.ID, fullName, email, last_app, last_activity, last_page, $apps_str
			(SELECT COUNT(DISTINCT global_publications.ID) FROM ab_users_pub INNER JOIN global_publications ON ab_users_pub.pID = global_publications.ID WHERE ab_users_pub.uID =global_users.ID ) AS publicationCount,
			(SELECT COUNT(DISTINCT global_companies.ID) FROM global_users_company INNER JOIN global_companies ON global_users_company.cID = global_companies.ID) AS companyCount

FROM global_users INNER JOIN global_users_company ON global_users.ID = global_users_company.uID

			$where
			 $orderby
			 $limit
		");


		$return = $result;

		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}
	public static function save($ID,$values){
		$timer = new timer();
		$user = F3::get("user");

		$cID = $values['cID'];
		if (!$cID){
			$cID = $user['publication']['cID'];
		}

			$a = new Axon("global_users");
		$a->load("ID='$ID'");

		foreach ($values as $key=> $value) {
			$a->$key = $value;
		}

		$a->save();

		if (!$a->ID) {
			$label = "User Added";
			$ID = $a->_id;
		} else {
			$label = "User Edited";
			$ID = $a->ID;
		}
		$app = F3::get("app");
		$appClass = "\\models\\" . $app . "\\publications";


		$p = new Axon($app."_users_pub");
		$publications = $appClass::getAll("cID='$cID'", "publication ASC");

		foreach($publications as $publication){
			$p->load("pID='".$publication['ID']."' AND uID='".$ID."'");
			if (in_array($publication['ID'], $values['publications'])){
				$p->pID = $publication['ID'];
				$p->uID = $ID;
				$p->save();
			} else {
				if (!$p->dry()){
					$p->erase();
				}
			}
			$p->reset();
		}






		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $ID;
	}

	public static function check_email($email) {


		$email = strtolower($email);
		$results = f3::get("DB")->exec("SELECT ID, fullName, email FROM global_users WHERE email = '$email'");

		if (count($results)) {
			$results = $results[0];
		} else {

		}
		return $results;

	}
	public static function _add_company($ID, $cID="") {
		$timer = new timer();
		$user = F3::get("user");
		if (!$cID) {
			$cID = $user['publication']['cID'];
		}
		$app = F3::get("app");
		$p = new Axon("global_users_company");
		$p->load("uID='$ID' AND cID='$cID'");

		$p->uID=$ID;
		$p->cID=$cID;
		//$p->$app = '1';

		$p->save();

		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return "done";
	}
	public static function _add_app($ID, $cID="",$app="") {
		$timer = new timer();
		$user = F3::get("user");
		if (!$cID) {
			$cID = $user['publication']['cID'];
		}
		$app = F3::get("app");
		$p = new Axon("global_users_company");
		$p->load("uID='$ID' AND cID='$cID'");

		$p->uID=$ID;
		$p->cID=$cID;
		$p->$app = '1';

		$p->save();

		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return "done";
	}
	public static function _remove_app($ID, $cID="",$app="") {
		$timer = new timer();
		$user = F3::get("user");
		if (!$cID) {
			$cID = $user['publication']['cID'];
		}
		if (!$app){
			$app = F3::get("app");
		}

		$p = new Axon("global_users_company");
		$p->load("uID='$ID' AND cID='$cID'");

		$p->uID=$ID;
		$p->cID=$cID;
		$p->$app = '0';

		$p->save();

		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return "done";
	}

	public static function _remove_company($ID, $cID="") {
		$timer = new timer();
		$user = F3::get("user");
		if (!$cID) {
			$cID = $user['publication']['cID'];
		}
		$app = F3::get("app");
		$p = new Axon("global_users_company");
		$p->load("uID='$ID' AND cID='$cID'");

		$p->uID=$ID;
		$p->cID=$cID;
		$p->$app = '0';
		$p->save();

		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return "done";
	}


	private static function dbStructure() {
		$table = F3::get("DB")->exec("EXPLAIN global_users;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}
		$result['settings'] = array();
		$result['pID'] = "";
		$result['publications'] = array();
		$result['publication'] = array();

		return $result;
	}
}
