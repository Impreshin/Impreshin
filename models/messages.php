<?php

namespace models;

use \timer as timer;

class messages {
	private $classname;

	function __construct() {

		$classname = get_class($this);
		$this->dbStructure = $classname::dbStructure();

	}

	function get($ID) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		$userID = $user['ID'];


		$result = $f3->get("DB")->exec("
			SELECT global_messages.*,
				(SELECT fullName FROM global_users WHERE global_messages.to_uID = global_users.ID) AS to_fullName,
				(SELECT fullName FROM global_users WHERE global_messages.from_uID = global_users.ID) AS from_fullName
			FROM global_messages
			WHERE ID = '$ID'
		");


		if (count($result)) {
			$return = $result[0];
			$return['datein'] = datetime($return['datein'],'',$user['company']['timezone']);
		} else {
			$return = $this->dbStructure;
		}
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function _count() {
		$timer = new timer();
		$f3 = \Base::instance();
		$app = $f3->get("app");
		$user = $f3->get("user");
		$uID = $user['ID'];
		$cID = $user['company']['ID'];

			$result = $f3->get("DB")->exec("
				SELECT count(ID) as unread
				FROM global_messages
				WHERE to_uID = '$uID' and `read` ='0' AND cID = '$cID';
			");

		$return = $result[0];
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getAll($where = "", $orderby = "datein DESC", $limit = "") {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
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

		$result = $f3->get("DB")->exec("
				SELECT global_messages.*,
					(SELECT fullName FROM global_users WHERE global_messages.to_uID = global_users.ID) AS to_fullName,
					(SELECT fullName FROM global_users WHERE global_messages.from_uID = global_users.ID) AS from_fullName
				FROM global_messages
				$where
				$orderby
				$limit
				;
			");
		$return = array();
		foreach ($result as $item){
			$item['datein'] = datetime($item['datein'],'',$user['company']['timezone']);
			$return[] = $item;
		}
		

		//$return = $result;
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}


	public static function _save($ID, $values, $options=array("dry"=>true)) {
		$timer = new timer();
		$f3 = \Base::instance();

		if (isset($values['message'])){
			$cfg = $f3->get("CFG");
			$values['message'] = $f3->scrub($values['message'], $cfg['nf']['whitelist_tags']);
			$values['message'] = preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i",'<$1$2>', $values['message']);
		}
		


		$a = new \DB\SQL\Mapper($f3->get("DB"),"global_messages");
		$a->load("ID='$ID'");

		foreach ($values as $key => $value) {
			if (isset($a->$key)) {
				$a->$key = $value;
			}
		}
		
		if (isset($options['dry'])){
			if ($options['dry']==false && $a->dry()){
				
			} else {
				$a->save();
			}
		} else {
			$a->save();
		}
		
		


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $ID;

	}

	private static function dbStructure() {
		$f3 = \Base::instance();
		$table = $f3->get("DB")->exec("EXPLAIN global_messages;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}
		
		return $result;
	}
}