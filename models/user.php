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


				$appClass = "\\models\\". $app."\\user_settings";
				$appO = new $appClass();
				$app = $appO->_read($result['ID']);


				if ((isset($_GET['apID']) && $_GET['apID']) && $_GET['apID'] != $app['pID']) {
					$appClass::save_config(array("pID"=> $_GET['apID']), $result['ID']);
					$app = $appO->_read($result['ID']);
				}









				$result['settings']= array_replace_recursive((array)$defaults, (array)($app['settings'])?unserialize($app['settings']):array());;


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
			}


		} else {
			$result = $this->dbStructure();
		}
		$return = $result;
		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
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
