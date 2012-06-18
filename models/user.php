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



		$defaults = F3::get("defaults");

		$result = F3::get("DB")->exec("
			SELECT *
			FROM global_users
			WHERE global_users.ID = '$ID'
		"
		);
		//echo $ID . "<br>";
		//print_r($result);echo "<hr>";

		if (count($result)){
			$result = $result[0];


			$app = F3::get("app");


			if ($app){
				F3::get("DB")->exec("UPDATE global_users SET last_app = '$app' WHERE ID = '". $result['ID']."'");


				$appClass = "\\models\\". $app."\\user_settings";
				$appO = new $appClass();
				$app = $appO->_read($result['ID']);


				if ((isset($_GET['apID']) && $_GET['apID']) && $_GET['apID'] != $app['pID']) {
					$appClass::save_config(array("pID"=> $_GET['apID']), $result['ID']);
					$app = $appO->_read($result['ID']);
				}





				$app_settings = $app['settings'];
				if ($app_settings){
					$settings = array_replace_recursive((array)$defaults, (array)($app_settings) ? unserialize($app_settings) : array());
					;
				} else {
					$settings = $defaults;
				}




				$result['settings']= $settings;



				$publications = ab\publications::getAll("uID='".$result['ID']."'","publication ASC");
				$pID = $publications[0]['ID'];


				$pubstr = array();
				$publication = "";
				foreach ($publications AS $pub) $pubstr[] = $pub["ID"];

				if (in_array($app['pID'], $pubstr)) {
					$pID = $app['pID'];

				}

				$publication = new \models\ab\publications();
				$publication = $publication->get($pID);

				$result['pID'] = $pID;
				$result['publications']=$publications;
				$result['publication']= $publication;
			} else {
				$result['settings'] = array();
				$result['pID'] = "";
				$result['publications'] = array();
				$result['publication'] = array();
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
