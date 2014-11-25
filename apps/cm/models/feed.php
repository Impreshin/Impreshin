<?php

namespace apps\cm\models;
use \timer as timer;

class feed {
	private $classname;

	function __construct() {
		$classname = get_class($this);
	}



	

	public static function getList($where = "", $orderby = "datein DESC") {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");

		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}
		$orderbysql = "";
		if ($orderby) {
			$orderbysql = " ORDER BY " . $orderby;
		}
		
		$result = array();
		//test_array($where);

		
		$interactions = $f3->get("DB")->exec("
			SELECT cm_companies_interactions.*, cm_companies.company as heading, global_users.fullName,  CONCAT('co-',cm_companies.ID) AS linkID
			FROM ((cm_companies_interactions INNER JOIN cm_companies ON cm_companies_interactions.parentID = cm_companies.ID) INNER JOIN cm_watchlist_companies ON cm_companies.ID = cm_watchlist_companies.companyID) INNER JOIN global_users ON cm_companies_interactions.uID = global_users.ID
			$where
			$orderbysql
		");
		$notes = $f3->get("DB")->exec("
			SELECT cm_companies_notes.*, cm_companies.company as heading, global_users.fullName,  CONCAT('co-',cm_companies.ID) AS linkID
			FROM ((cm_companies_notes INNER JOIN cm_companies ON cm_companies_notes.parentID = cm_companies.ID) INNER JOIN cm_watchlist_companies ON cm_companies.ID = cm_watchlist_companies.companyID) INNER JOIN global_users ON cm_companies_notes.uID = global_users.ID
			$where
			$orderbysql
		");

		$orderby = explode(" ",$orderby);

		$app_settings = \apps\cm\settings::_available();
		$types = $app_settings['interaction_types_icons'];
		//test_array($app_settings); 

		

		
		
		foreach ($interactions as $item){
			$item['type'] = 'interaction';
			$item['icon'] = isset($types[$item['typeID']])?$types[$item['typeID']]:"";
			$result[$item[$orderby[0]]] = $item;
		}
		foreach ($notes as $item){
			$item['type'] = 'note';
			$item['icon'] = 'icon-flag';
			$item['text'] = $item['note'];
			unset($item['note']);
			
			$result[$item[$orderby[0]]] = $item;
		}
		
		if ($orderby[0]=="ASC"){
			ksort($result);
		} else {
			krsort($result);
		}

		$n = array();
		foreach ($result as $item){
			$item['text'] = nl2br($item['text']);
			$item['timeago'] = timesince($item['datein']);
			
			$n[] = $item;
		}
		$result = $n;
		//test_array($result); 
		
		
		
		$return = $result;
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

}