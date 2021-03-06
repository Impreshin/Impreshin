<?php
/*
 * Date: 2011/11/16
 * Time: 11:29 AM
 */
namespace models;



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
		$f3 = \Base::instance();
		$result = $f3->get("DB")->exec("
				SELECT global_users.*
				FROM global_users
				WHERE global_users.ID = '$ID'
			");
		if (count($result)) {
			$result = $result[0];
		} else {
			$result = $this->dbStructure();
		}

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $result;
	}

	private static function appSettings($uID, $app, $pID) {
		$f3 = \Base::instance();
		$table = $app . "_users_settings";
		$data = $f3->get("DB")->exec("SELECT * FROM $table WHERE uID = '$uID'");
		$settingsClass = "\\models\\" . $app . "\\settings";
		$permissionsClass = "\\models\\" . $app . "\\user_permissions";
		$defaults = $settingsClass::defaults();
		if (count($data)) {
			$data = $data[0];
			$user_settings = @unserialize($data['settings']);
			if ($user_settings) {
				$user_settings = array_replace_recursive((array)$defaults, (array)($user_settings) ? $user_settings : array());
			} else {
				$user_settings = $defaults;
			}

			$data['settings'] = $user_settings;

		} else {
			$data = array(
				"settings"      => $defaults,
				"pID"           => "",
				"last_activity" => ""
			);
		}
		$return = array(
			"settings"      => $data['settings'],
			"last_activity" => $data['last_activity'],
			"access"        => false,
			"permissions"   => array(),
			"extra"         => array()
		);


		$appstuff = $f3->get("DB")->exec("SELECT * FROM global_users_company WHERE uID = '$uID' AND cID = (SELECT cID FROM global_publications WHERE global_publications.ID = '$pID') ORDER BY ID DESC LIMIT 0,1");


		if (count($appstuff)) {
			$appstuff = $appstuff[0];

			//	test_array($appstuff);
			$return['access'] = ($appstuff[$app] == '1') ? true : false;
			$return['permissions'] = $appstuff[$app . '_permissions'];


			$e = array();
			foreach (array_keys($appstuff) as $value) {
				if (substr($value, 0, 3) == $app . "_") {
					$e[$value] = $appstuff[$value];
				}
			}
			unset($e[$app . "_permissions"]);


			$return['extra'] = $e;
		}

		$return['permissions'] = $permissionsClass::_read($return['permissions']);

		return $return;
	}

	public function user($user = "") {
		$f3 = \Base::instance();
		$timer = new timer();
		$app = $f3->get("app");
		$cfg = $f3->get("CFG");
		$result = array();

		if (!is_array($user)) {
			$user = $this->get($user);
		}


		$result = $user;

		$uID = $user['ID'];
		$appSpecific = array();


		if ($app && $user['ID'] && in_array($app, $cfg['apps'])) {



			$table = $app . "_users_settings";
			$lastpID = $f3->get("DB")->exec("SELECT pID FROM $table WHERE uID = '$uID'");
			if (count($lastpID)) {
				$lastpID = $lastpID[0]['pID'];
			} else {
				$lastpID = "";
			}


			if ((isset($_GET['apID']) && $_GET['apID']) && $_GET['apID'] != $lastpID) {
				$appClass = "\\models\\" . $app . "\\user_settings";
				$appO = new $appClass();
				$appClass::save_config(array("pID" => $_GET['apID']), $result['ID']);
				$lastpID = $_GET['apID'];
			}

			if ($result['su'] == '1') {
				$publications = publications::getAll("", "company ASC, publication ASC");
			} else {
				$publications = publications::getAll_user("global_users_company.uID='" . $result['ID'] . "' and [access] = '1'", "company ASC, publication ASC");
			}

			$pID = (count($publications)) ? $publications[0]['ID'] : "";


			$pubstr = array();
			$publication = "";
			foreach ($publications AS $pub) {
				$pubstr[] = $pub["ID"];
			}

			if (in_array($lastpID, $pubstr)) {
				$pID = $lastpID;

			}


			$publication = new publications();
			$publication = $publication->get($pID);
			$cID = $publication['cID'];
			if (!$publication['ID']) {
				$companies_user = \models\company::getAll_user($user['ID']);
				if (count($companies_user))	$cID = $companies_user[0]['ID'];
				//test_array($companies_user);
			}
			$companyObject = new company();
			$company = $companyObject->get($cID);


			if ($publication['ID']==""){
				$uri = $_SERVER['REQUEST_URI'];
				$folder = "";
				if ($uri) {
					$uri = explode("/", $uri);
					$folder = isset($uri[1]) ? $uri[1] : "";

					if (strpos($folder, "?")) {
						$folder = explode("?", $folder);
						$folder = isset($folder[0]) ? $folder[0] : "";
					}


				}
				//test_array(\models\company::getAll_user("global_users_company.uID='" . $user['ID'] . "' and allow_setup ='1'"));
				if ($folder!=="setup"){

					//test_array($folder);
					if (count(\models\company::getAll_user("global_users_company.uID='" . $user['ID'] . "' and allow_setup ='1'"))!=0) {
						$f3->reroute("/setup");
					}
				}



			}





			if (isset($company[$app]) && $company[$app] != '1') {
				$f3->reroute("/noaccess/?app=$app&cID=" . $publication['cID']);
			}

			$result['pID'] = $pID;
			$result['publications'] = $publications;
			$result['publication'] = $publication;
			$result['company'] = $company;


			$appSettings = self::appSettings($uID, $app, $pID);


			//test_array($publications);

			$result['access'] = $appSettings['access'];
			$result['settings'] = $appSettings['settings'];


			//test_array($appSettings['access']);
			if (!$appSettings['access'] && $result['su'] != '1') {
			//	$f3->reroute("/noaccess/?app=$app&cID=" . $publication['cID']);
			}
			unset($result['password']);

			if ($app == "ab") {
			if (isset($appSettings['extra']['ab_marketerID']) && $appSettings['extra']['ab_marketerID']) $result['ab_marketerID'] = $appSettings['extra']['ab_marketerID'];
			if (isset($appSettings['extra']['ab_productionID']) && $appSettings['extra']['ab_productionID']) $result['ab_productionID'] = $appSettings['extra']['ab_productionID'];


			//unset($result[$app . '_permissions']);


				if (isset($appSettings['extra']['ab_marketerID']) && $appSettings['extra']['ab_marketerID']) {
					$marketer = \models\ab\marketers_targets::_current($appSettings['extra']['ab_marketerID'], $result['publication']['ID']);
				} else {
					$marketer = array();
				}

				//test_array($marketer);
				if (count($marketer)) {
					$result['marketer'] = $marketer;
				}
			}




			if ($result['su'] == '1') {
				$appClass = "\\models\\" . $app . "\\user_permissions";
				$permissions = $appClass::permissions();
				$permissions = $permissions['p'];
				array_walk_recursive($permissions, function (& $item, $key) {
						$item = "1";
					});
				$permissions['view']['only_my_records'] = '0';
			} else {
				$permissions = $appSettings['permissions'];


			}


			$permissions['administration']['_nav'] = '0';
			foreach ($permissions['administration']['application'] as $p) {
				if ($p['page']) $permissions['administration']['_nav'] = '1';
			}
			foreach ($permissions['administration']['system'] as $p) {
				if ($p['page']) $permissions['administration']['_nav'] = '1';
			}
			$permissions['reports']['_nav'] = '0';
			foreach ($permissions['reports'] as $k => $p) {

				if (isset($p['page']) && $p['page']) $permissions['reports']['_nav'] = '1';


				if (is_array($p)) {
					foreach ($p as $s => $ps) {
						if (isset($ps['page']) && $ps['page']) {
							$permissions['reports'][$k]['_nav'] = '1';
							$permissions['reports']['_nav'] = '1';
						}
					}
				}

			}


			if ($app == "ab") {
				$permissions['records']['_nav'] = '0';
				foreach ($permissions['records'] as $p) {
					if ($p['page']) $permissions['records']['_nav'] = '1';
				}

				if (isset($result['marketer']['ID']) && $result['marketer']['ID']) {
					$permissions['reports']['_nav'] = '1';
					$permissions['reports']['marketer']['_nav'] = '1';
					foreach ($permissions['reports']['marketer'] as $k => $p) {
						$permissions['reports']['marketer'][$k]['spage'] = '1';

					}

				}
			}


			$permissions['allow_setup'] = count(\models\company::getAll_user("global_users_company.uID='" . $user['ID'] . "' and allow_setup ='1'"))==0?"0":"1";

			$result['permissions'] = $permissions;


		} else {
			$result['settings'] = array();
			$result['pID'] = "";
			$result['publications'] = array();
			$result['publication'] = array();
			$result['permissions'] = array();

		}

		//test_array($result);
		//test_array($result);

		$return = $result;
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}


	function login($username, $password) {
		$f3 = \Base::instance();
		$timer = new timer();

		$ID = "";


		setcookie("username", $username, time() + 31536000, "/");


		$password_hash = $password;

		$password_hash = md5("aws_" . $password . "_" . md5("zoutnet"));


		$result = $f3->get("DB")->exec("
			SELECT ID, email FROM global_users WHERE email =:username AND password = :password
		",array(":username"=>$username,":password"=>$password_hash));


		if (count($result)) {
			$result = $result[0];
			$ID = $result['ID'];
			$_SESSION['uID'] = $ID;
			if (isset($_COOKIE['username'])) {
				$_COOKIE['username'] = $result['email'];
			} else {
				setcookie("username", $result['email'], time() + 31536000, "/");
			}
		}

		$return = $ID;
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	static function getAll($where = "", $orderby = "fullName ASC", $limit = "",$pID="") {
		$f3 = \Base::instance();
		$timer = new timer();
		$user = $f3->get("user");

		$pID = $pID ? $pID : $user['publication']['ID'];
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
		$apps = $f3->get("CFG");
		$apps = $apps['apps'];
		$app = (isset($_REQUEST['app'])&&in_array($_REQUEST['app'], $apps))? $_REQUEST['app']: $f3->get("app");

		$apps_str = "";
		if ($app){
			$apps_str = "global_users_company." . $app . ", (SELECT last_activity FROM " . $app . "_users_settings WHERE " . $app . "_users_settings.uID = global_users.ID) as " . $app . "_last_activity,  if ((SELECT count(ID) FROM " . $app . "_users_pub WHERE " . $app . "_users_pub.uID = global_users.ID AND " . $app . "_users_pub.pID = '$pID' LIMIT 0,1)<>0,1,0) as currentPub, ";
		}

	


		$result = $f3->get("DB")->exec("
			SELECT global_users.ID, fullName, email, last_app, last_activity, last_page, global_users_company.*, global_users.ID as ID, $apps_str
			(SELECT COUNT(DISTINCT global_publications.ID) FROM ab_users_pub INNER JOIN global_publications ON ab_users_pub.pID = global_publications.ID WHERE ab_users_pub.uID =global_users.ID ) AS publicationCount,
			(SELECT COUNT(DISTINCT global_companies.ID) FROM global_users_company INNER JOIN global_companies ON global_users_company.cID = global_companies.ID) AS companyCount

FROM global_users INNER JOIN global_users_company ON global_users.ID = global_users_company.uID

			$where
			 $orderby
			 $limit
		");


		$return = $result;
		
		

		$timer->stop(array( "Models" => array(
			                  "Class"  => __CLASS__,
			                  "Method" => __FUNCTION__
		                  )
		             ), func_get_args());
		return $return;
	}

	public static function save($ID, $values) {
		$f3  = \Base::instance();
		$timer = new timer();
		$user = $f3->get("user");

		if (isset($values['password']) && $values['password']) {
			$values['password'] = md5("aws_" . $values['password'] . "_" . md5("zoutnet"));
		}


		$cID = $values['cID'];
		if (!$cID) {
			$cID = $user['company']['ID'];
		}


		$a = new \DB\SQL\Mapper($f3->get("DB"),"global_users");
		$a->load("ID='$ID'");

		foreach ($values as $key => $value) {
			if (isset($a->$key)){
				$a->$key = $value;
			}


		}
		//test_array($user);
		$a->save();

		if (!$a->ID) {
			$label = "User Added";
			$ID = $a->_id;
		} else {
			$label = "User Edited";
			$ID = $a->ID;
		}
		$app = $f3->get("app");
		$appClass = "\\models\\" . $app . "\\publications";


		$p = new \DB\SQL\Mapper($f3->get("DB"),$app . "_users_pub");
		$publications = \models\publications::getAll("cID='$cID'", "publication ASC");

		foreach ($publications as $publication) {
			$p->load("pID='" . $publication['ID'] . "' AND uID='" . $ID . "'");
			if (in_array($publication['ID'], $values['publications'])) {
				$p->pID = $publication['ID'];
				$p->uID = $ID;
				$p->save();
			} else {
				if (!$p->dry()) {
					$p->erase();
				}
			}
			$p->reset();
		}


		$timer->stop(array(
		                  "Models" => array(
			                  "Class"  => __CLASS__,
			                  "Method" => __FUNCTION__
		                  )
		             ), func_get_args());
		return $ID;
	}

	public static function check_email($email) {

		$f3 = \Base::instance();

		$email = strtolower($email);
		$results = $f3->get("DB")->exec("SELECT ID, fullName, email FROM global_users WHERE email = '$email'");

		if (count($results)) {
			$results = $results[0];
		} else {

		}
		return $results;

	}

	public static function _add_company($ID, $cID = "") {
		$timer = new timer();
		$f3 = \Base::instance();

		$user = $f3->get("user");
		if (!$cID) {
			$cID = $user['company']['ID'];
		}
		$app = $f3->get("app");
		$p = new \DB\SQL\Mapper($f3->get("DB"),"global_users_company");
		$p->load("uID='$ID' AND cID='$cID'");

		$p->uID = $ID;
		$p->cID = $cID;
		//$p->$app = '1';

		$p->save();

		$timer->stop(array(
		                  "Models" => array(
			                  "Class"  => __CLASS__,
			                  "Method" => __FUNCTION__
		                  )
		             ), func_get_args());
		return "done";
	}

	public static function _add_app($ID, $cID = "", $app = "") {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		if (!$cID) {
			$cID = $user['company']['ID'];
		}
		$app = $f3->get("app");
		$p = new \DB\SQL\Mapper($f3->get("DB"),"global_users_company");
		$p->load("uID='$ID' AND cID='$cID'");

		$p->uID = $ID;
		$p->cID = $cID;
		$p->$app = '1';

		$p->save();

		$timer->stop(array(
		                  "Models" => array(
			                  "Class"  => __CLASS__,
			                  "Method" => __FUNCTION__
		                  )
		             ), func_get_args());
		return "done";
	}

	public static function _remove_app($ID, $cID = "", $app = "") {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		if (!$cID) {
			$cID = $user['company']['ID'];
		}
		if (!$app) {
			$app = $f3->get("app");
		}

		$p = new \DB\SQL\Mapper($f3->get("DB"),"global_users_company");
		$p->load("uID='$ID' AND cID='$cID'");

		$p->uID = $ID;
		$p->cID = $cID;
		$p->$app = '0';

		$p->save();

		$timer->stop(array(
		                  "Models" => array(
			                  "Class"  => __CLASS__,
			                  "Method" => __FUNCTION__
		                  )
		             ), func_get_args());
		return "done";
	}

	public static function _remove_company($ID, $cID = "") {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		if (!$cID) {
			$cID = $user['company']['ID'];
		}
		$app = $f3->get("app");
		$p = new \DB\SQL\Mapper($f3->get("DB"),"global_users_company");
		$p->load("uID='$ID' AND cID='$cID'");

		$p->uID = $ID;
		$p->cID = $cID;
		$p->$app = '0';
		$p->save();

		$timer->stop(array(
		                  "Models" => array(
			                  "Class"  => __CLASS__,
			                  "Method" => __FUNCTION__
		                  )
		             ), func_get_args());
		return "done";
	}


	private static function dbStructure() {
		$f3 = \Base::instance();
		$table = $f3->get("DB")->exec("EXPLAIN global_users;");
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
