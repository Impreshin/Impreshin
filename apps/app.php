<?php
/**
 * User: William
 * Date: 2013/07/17 - 12:34 PM
 */
namespace apps;
use \models as models;
use \timer as timer;
class app {

	function __construct() {
		$this->f3 = \base::instance();
		$this->user = $this->f3->get("user");
		$this->namespace = __NAMESPACE__;


		$this->timer = new timer();

	}

	function __destruct() {
		$this->timer->stop("app");
	}
	function current_app(){
		$app = $this->namespace;




		$app = explode("\\", $app);
		$app = $app[1];
		$this->f3->set("app", $app);

		return $app;
	}

	function access() {
		$app = $this->current_app();
		$reroute = "";
		if (!$this->user['ID'] && $reroute=="") $reroute = "/login";
		if (!$this->user['access'] && $reroute=="") $reroute = "/app/$app/access";



		if ($reroute){
			$this->f3->set("stopchain",true);
			if ($this->f3->get("AJAX")) {

				test_array(array("reroute"=>$reroute));
			} else {
				$this->f3->reroute($reroute);
			}


		} else {
		}


	}

	function last_page() {
		$user = $this->user;
		$this->f3->get("DB")->exec("UPDATE global_users SET last_page = '" . $_SERVER['REQUEST_URI'] . "' WHERE ID = '" . $user['ID'] . "'");

		$app = $this->namespace;
		$app = str_replace("apps\\", "", $app);

		$table = $app . "_users_settings";
		$this->f3->get("DB")->exec("UPDATE $table SET last_page = '" . $_SERVER['REQUEST_URI'] . "' WHERE uID = '" . $user['ID'] . "'");
	}

	function user() {
		$timer = new timer();
		$user = $this->user;
		$uID = $user['ID'];



		$app = $this->current_app();
		/*** lookup the settings stored for the user for the current app ***/
		$table = $app . "_users_settings";

		$data = $this->f3->get("DB")->exec("SELECT * FROM $table WHERE uID = '$uID'");


		$DefaultsettingsClass = $this->namespace . "\\settings";
		$settings = $DefaultsettingsClass::defaults();

		if (count($data)) {
			$data = $data[0];
			$user_settings = @unserialize($data['settings']);
			$settings = array_replace_recursive((array)$settings, (array)($user_settings) ? $user_settings : array());
		} else {
			$table = $this->f3->get("DB")->exec("EXPLAIN $table;");
			$result = array();
			foreach ($table as $key => $value) {
				$result[$value["Field"]] = "";
			}

			$data =  $result;
		}



		$data['settings'] = $settings;




		$return = $user;
		$return["settings"] = $data['settings'];
		$return["last_activity"] = $data['last_activity'];
		$return["access"] = false;
		$return["permissions"] = array();
		$return["extra"] = array();


		$lastpID = $data['pID'];
		if ((isset($_GET['apID']) && $_GET['apID']) && $_GET['apID'] != $lastpID) {
			//$settingsClass::save($uID,array("pID" => $_GET['apID']));
			$this->f3->get("DB")->exec("UPDATE $table SET pID = '". $_GET['apID']."' WHERE uID = '$uID'");
			$lastpID = $_GET['apID'];
		}







		/*** get a list of publications and create an array to find out of the user can access the publication or not***/

		if ($user['su'] == '1') {
			$publications = models\publications::getAll("", "company ASC, publication ASC");
		} else {
			$publications = models\publications::getAll_user("global_users_company.uID='" . $user['ID'] . "' and [access] = '1'", "company ASC, publication ASC");
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
		//test_array($pID);

		$publication = new models\publications();
		$publication = $publication->get($pID);
		$cID = $publication['cID'];
		if (!$publication['ID']) {
			$companies_user = \models\company::getAll_user($user['ID']);
			if (count($companies_user)) $cID = $companies_user[0]['ID'];
			//test_array($companies_user);
		}
		$companyObject = new models\company();
		$company = $companyObject->get($cID);


		$return['pID'] = $pID;
		$return['publications'] = $publications;
		$return['publication'] = $publication;
		$return['company'] = $company;


		$DefaultPermissionsClass = $this->namespace . "\\permissions";
		$permissions = $DefaultPermissionsClass::defaults();
		$permissions = $permissions["p"];

		/*** find company specific settings for the user like permissions and access to the various apps **/

		$appstuff = $this->f3->get("DB")->exec("SELECT * FROM global_users_company WHERE uID = '$uID' AND cID = (SELECT cID FROM global_publications WHERE global_publications.ID = '$lastpID') ORDER BY ID DESC LIMIT 0,1");

		$return['access'] = false;
		$applications_list = $this->f3->get("applications");
		$applications = array();
		if (count($appstuff)) {
			$appstuff = $appstuff[0];

			//	test_array($appstuff);
			$return['access'] = ($appstuff[$app] == '1') ? true : false;





			$permissions = self::permissions_read($appstuff[$app . '_permissions']);





			$e = array();
			foreach (array_keys($appstuff) as $value) {
				$a  = explode("_", $value);
				if ( $a[0] == $app && $value != $app) {
					$e[$value] = $appstuff[$value];
				}

			}
			foreach ($applications_list as $a=>$v) {
				if (isset($appstuff[$a]) && $appstuff[$a]=='1'){
					$applications[$a] = $applications_list[$a];
				}
			}

			unset($e[$app . "_permissions"]);


			$return['extra'] = $e;
		}





		if ($user['su'] == '1') {
			//$applications = $applications_list;
			array_walk_recursive($permissions, function (& $item, $key) {
					$item = "1";
				}
			);

			$return['access'] = true;
		}


		$return['applications'] = $applications;
		//test_array($return);




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

		//test_array($return);

		if ($app == "ab") {

			if ($user['su'] == '1') {
				$permissions['view']['only_my_records'] = '0';
			}


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


	//	test_array($permissions);
		$return['permissions'] = $permissions;
		//test_array($return);
		$timer->stop(array("App" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}


	public static function permissions_read($data){
		$timer = new timer();


		$permissions = \apps\ab\permissions::defaults();
		$permissions = $permissions["p"];


		if (count($data)) {
			$user_permissions = @unserialize($data);
			$user_permissions = array_replace_recursive((array)$permissions, (array)($user_permissions) ? $user_permissions : array());
		} else {
			$user_permissions = $permissions;
		}


		$return = array();

		//test_array($user_settings);
		$return = $user_permissions;


		$timer->stop(array("App" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function permissions_save($uID, $cID, $values) {
		$timer = new timer();
		$f3 = \Base::instance();

		$permission_column = $f3->get("app");

		$permission_column = $permission_column . "_permissions";

		$a = new \DB\SQL\Mapper($f3->get("DB"), "global_users_company");
		$a->load("uID='$uID' AND cID = '$cID'");
		$a->$permission_column = serialize($values);

		if (!$a->dry()) $a->save();


		$timer->stop(array("App" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return "done";
	}
}
