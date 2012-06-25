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


		$app = F3::get("app");

		$a = array();
		$a['layout']['page']['t2']="abc";
		//$t = \models\ab\user_permissions::write("2","1",$a);




		$defaults = F3::get("defaults");
		if (!$app) {

			$result = F3::get("DB")->exec("
				SELECT global_users.*
				FROM global_users
				WHERE global_users.ID = '$ID'
			");
		} else {
			$result = F3::get("DB")->exec("
				SELECT *, global_users.ID as ID
				FROM global_users INNER JOIN global_users_company ON global_users.ID = global_users_company.uID
				WHERE uID = '$ID' AND `$app` = '1'
			");
		}

		//echo $ID . "<br>";
		//print_r($result);echo "<hr>";

		if (count($result)){
			$result = $result[0];





			if ($app){

				if (!$result[$app]) F3::error(404);








				$appClass = "\\models\\". $app."\\user_settings";
				$appO = new $appClass();
				$appSettings = $appO->_read($result['ID']);




				if ((isset($_GET['apID']) && $_GET['apID']) && $_GET['apID'] != $appSettings['pID']) {
					$appClass::save_config(array("pID"=> $_GET['apID']), $result['ID']);
					$appSettings = $appO->_read($result['ID']);
				}


				$appPublications = "\\models\\" . $app . "\\publications";

				$publications = $appPublications::getAll_user("uID='".$result['ID']."'","publication ASC");
				$pID = (count($publications))? $publications[0]['ID']:"";


				$pubstr = array();
				$publication = "";
				foreach ($publications AS $pub) $pubstr[] = $pub["ID"];

				if (in_array($appSettings['pID'], $pubstr)) {
					$pID = $appSettings['pID'];

				}


				$publication = new $appPublications();
				$publication = $publication->get($pID);

				$result['pID'] = $pID;
				$result['publications']=$publications;
				$result['publication']= $publication;

				$appClass = "\\models\\" . $app . "\\user_permissions";
				$result['permissions'] = $appClass::_read($result[$app.'_permissions']);

				unset($result['password']);
				unset($result[$app.'_permissions']);

			} else {
				$result['settings'] = array();
				$result['pID'] = "";
				$result['publications'] = array();
				$result['publication'] = array();
				$result['permissions'] = array();

			}


		} else {
			$result = $this->dbStructure();
		}



		//test_array($result);
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
			$apps_str .= "global_users_company.". $app.", (SELECT last_activity FROM ".$app."_users_settings WHERE " . $app . "_users_settings.uID = global_users.ID) as " . $app . "_last_activity, ";
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







		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $ID;
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
