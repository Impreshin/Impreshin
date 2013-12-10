<?php
/**
 * User: William
 * Date: 2012/07/03 - 2:47 PM
 */
namespace apps\nf\models;



use \timer as timer;

class notifications {
	public static function show() {
		$return = array();
		$return['footer'] = self::bar();

		return $return;
	}

	public static function bar() {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		$return = $records = array();

		$userID = $user['ID'];



		$where = "nf_articles.cID ='".$user['company']['ID']."'  AND nf_articles.deleted is null ";
		if (($user['permissions']['view']['only_my_records'] == '1')) {
			$where = $where . " AND authorID = '" . $user['ID'] . "'";
		}




		//$where = "1";
		$where .= " AND (archived != '1' || stageID !='2') AND (nf_articles.rejected !='1' || authorID='$userID')";
		$options = array();


		$grouping = array("g"=>"none","o"=>"ASC");
		$ordering = array("c"=>"nf_stages.orderby","o"=>"ASC");


		$records = articles::getAll($where, $grouping, $ordering,$options);
		$stats = record_stats::stats($records,array("locked","stages","in_newsbook","placed"));

		$return['current'] = $stats;
		
		//test_array($stats); 


		if (!count($return)) $return = false;
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

}
