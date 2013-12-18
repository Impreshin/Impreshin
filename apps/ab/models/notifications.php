<?php
/**
 * User: William
 * Date: 2012/07/03 - 2:47 PM
 */
namespace apps\ab\models;



use \timer as timer;

class notifications {
	public static function show() {
		$return = array();
		$return['footer'] = self::bar();
		$return['messages'] = \models\messages::_count();
		return $return;
	}

	public static function bar() {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		$return = $records = array();

		if ($user['ID'] && isset($user['publication']['ID']) && isset($user['publication']['current_date']['ID'])) {

			
			if (isset($user['extra']['ab_marketerID']) && $user['extra']['ab_marketerID']) {
				$marketer = \apps\ab\models\marketers_targets::_current($user['extra']['ab_marketerID'], $user['publication']['ID']);
			} else {
				$marketer = array();
				if (isset($user['marketer'])) {
					$marketer = $user['marketer'];
				}
			}

			
			if (count($marketer)) {
				$return['marketer'] = $marketer;
			}



			

			if ((isset($user['ab_productionID']) && $user['ab_productionID']) || $user['permissions']['details']['actions']['check'] || $user['permissions']['layout']['page']) {
				$records = bookings::getAll("ab_bookings.pID = '" . $user['publication']['ID'] . "' AND ab_bookings.dID = '" . $user['publication']['current_date']['ID'] . "' AND ab_bookings.deleted is null");
			}

			if ($user['permissions']['details']['actions']['check']) {
				$checked = 0;
				$recordsCount = 0;
				foreach ($records as $record) {
					$recordsCount++;
					if ($record['checked']) $checked++;

				}

				$return['checked'] = array(
					"total"   => $recordsCount,
					"done"    => $checked,
					"percent" => ($checked && $recordsCount)? number_format((($checked / $recordsCount) * 100), 2):""
				);
			}

			if ($user['permissions']['layout']['page']) {
				$done = 0;
				$recordsCount = 0;
				foreach ($records as $record) {
					if ($record['checked'] && $record['typeID'] == '1') {
						$recordsCount++;
						if ($record['pageID']) $done++;
					}


				}

				$return['placed'] = array(
					"total"   => $recordsCount,
					"done"    => $done,
					"percent" => ($done && $recordsCount)? number_format((($done / $recordsCount) * 100), 2):""
				);
			}


			if (isset($user['ab_productionID']) && $user['ab_productionID']) {


				$assigned = 0;
				$assigned_done = 0;
				$done = 0;
				$recordsCount = 0;
				foreach ($records as $record) {
					if ($record['typeID'] == '1') {
						$recordsCount++;
						if ($record['material_productionID'] == $user['ab_productionID']) {
							$assigned++;
							if ($record['material_status']) {
								$assigned_done++;
							}
						}
						if ($record['material_status']) {
							$done++;
						}
					}

				}

				$remaining = $recordsCount - $done;
				$return['production'] = array(
					"records"  => array(
						"total"   => $recordsCount,
						"done"    => $done,
						"percent" => ($remaining) ? number_format((($done / $recordsCount) * 100), 2) : ""
					),
					"assigned" => array(
						"total"   => $assigned,
						"done"    => $assigned_done,
						"percent" => ($assigned - $assigned_done) ? number_format((($assigned_done / $assigned) * 100), 2) : ""
					)

				);

			}
		}


		if (!count($return)) $return = false;
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

}
