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
		$return['messages'] = \models\messages::_count();

		$f3 = \Base::instance();
		$user = $f3->get("user");
		$cID = $user['company']['ID'];

		$users = \models\user::getAll("cID='$cID' AND COALESCE(last_activity, 0) > CURDATE() - INTERVAL 2 HOUR", "last_activity DESC, fullName ASC");

		$applications_list = $f3->get("applications");
		
		$l = array();
		foreach ($users as $u){
			$app = "";
			foreach ($applications_list as $k=>$v){
				$start = "/app/".$k;

				if (substr($u['last_page'],0,strlen($start))==$start){
					$app = $k;
				}
			}
			
			
			$l[] = array(
				"ID"=>$u['ID'],
				"f"=>$u['fullName'],
				"t"=>timesince($u['last_activity']),
				"d"=>$u['last_activity'],
				"p"=>$u['last_page'],
				"a"=>strtoupper($app)
			);
		}
		
		//test_array(array("users"=>$l,"apps"=>$applications_list)); 
		$return['users'] = $l;

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
