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

		if (isset($user['marketer'])) {
			$return['marketer'] = $user['marketer'];
		}

		if (isset($user['ab_productionID']) && $user['ab_productionID']){

			$records = bookings::getAll("ab_bookings.pID = '". $user['publication']['ID'] ."' AND ab_bookings.dID = '". $user['publication']['current_date']['ID'] ."'");

			$assigned = 0;
			$assigned_done = 0;
			$done = 0;
			foreach ($records as $record){
				if ($record['material_productionID']== $user['ab_productionID']){
					$assigned++;
					if ($record['material_status']) {
						$assigned_done++;
					}
				}
				if ($record['material_status']) {
					$done++;
				}
			}
			$recordsCount = count($records);
			$remaining = $recordsCount - $done;
			$return['production'] = array(
				"records"=> array(
					"total"=> $recordsCount,
					"done"=>$done,
					"percent"=> ($remaining) ? number_format((($done / $recordsCount) * 100), 2) : ""
				),
				"assigned"=>array(
					"total"=>$assigned,
					"done"=>$assigned_done,
					"percent"=> ($assigned - $assigned_done) ? number_format((($assigned_done / $assigned) * 100), 2) : ""
				)

			);

		}



		if (!count($return))$return= false;
		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}

}
