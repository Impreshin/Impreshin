<?php
/**
 * User: William
 * Date: 2012/07/03 - 2:47 PM
 */
namespace models\ab;
use \F3 as F3;
use \Axon as Axon;
use \timer as timer;
class user_notifications {
	public static function show(){
		$timer = new timer();
		$user = F3::get("user");

		$return = array();

		if (count($user['marketer'])) {
			$return['marketer'] = $user['marketer'];
		}

		if ($user['ab_productionID']){

			$records = bookings::getAll("material_productionID='". $user['ab_productionID']."' AND ab_bookings.pID = '". $user['publication']['ID'] ."' AND ab_bookings.dID = '". $user['publication']['current_date']['ID'] ."'");

			$t = 0;
			foreach ($records as $record){
				if ($record['material_status']){
					$t = $t + 1;
				}
			}
			$recordsCount = count($records);
			$remaining = $recordsCount - $t;
			$return['production'] = array(
				"records"=> $recordsCount,
				"done"=>$t,
				"remaining"=> $remaining,
				"percent"=>($remaining)? number_format((($t/ $recordsCount)*100),2):""
			);

		}



		if (!count($return))$return= false;
		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}

}
