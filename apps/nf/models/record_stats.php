<?php
/**
 * User: William
 * Date: 2012/05/29 - 2:11 PM
 */
namespace apps\nf\models;

class record_stats {
	public static function stats($where, $columns = array("cm"), $pages = "") { // "cm","checked","material","material_approved","layout","placed","placed_cm"
		$timer = new \timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");

//$columns = array("cm","placed","placed_cm");
		if (is_array($where)) {
			$data = $where;
		} else {
			$data = \apps\nf\models\articles::getAll($where);

		}

		//test_array($data); 
		$totals = array(
			"records" => count($data),
		);

		if (in_array("locked", $columns)) $totals['locked'] = 0;
		if (in_array("stages", $columns)) $totals['stages'] = array();
		if (in_array("in_newsbook", $columns)) $totals['in_newsbook'] = 0;
		if (in_array("placed", $columns)) $totals['placed'] = 0;
		if (in_array("ready", $columns)) $totals['ready'] = 0;
		

		$lastdID_ = "";
		$maxPages = 0;

		$dIDArray = array();
		foreach ($data as $record) {
			

			if (in_array("locked", $columns)) if ($record['locked_fullName']) $totals['locked'] = $totals['locked'] + 1;
			if (in_array("stages", $columns)) {
				if ($record['stage']) {
					if (!isset($totals['stages'][$record['stage']])){
						$totals['stages'][$record['stage']] = 0;
					}
					$totals['stages'][$record['stage']] = $totals['stages'][$record['stage']] + 1;
				}
			}
			if (in_array("in_newsbook", $columns)) if (isset($record['in_newsbook'])&&$record['in_newsbook']) $totals['in_newsbook'] = $totals['in_newsbook'] + 1;
			if (in_array("placed", $columns)) if (isset($record['placed'])&&$record['placed']) $totals['placed'] = $totals['placed'] + 1;
			if (in_array("ready", $columns)) if (isset($record['ready'])&&$record['ready']) $totals['ready'] = $totals['ready'] + 1;


		}
		//$totals['dates']= $dIDArray;
		//test_array($dIDArray);


		//test_array($maxPages);
		

		$return = array(
			"records" => array(
				"total" => $totals["records"],
			)


		);


		if (in_array("locked", $columns)) {
			$return['records']['locked'] = array(
				"r" => $totals["locked"],
				"p" => ($totals['locked']) ? number_format((($totals["locked"] / $totals["records"]) * 100), 2) : 0
			);
		}
		if (in_array("in_newsbook", $columns)) {
			$return['records']['in_newsbook'] = array(
				"r" => $totals["in_newsbook"],
				"p" => ($totals['in_newsbook']) ? number_format((($totals["in_newsbook"] / $totals["records"]) * 100), 2) : 0
			);
		}
		if (in_array("placed", $columns)) {
			$return['records']['placed'] = array(
				"r" => $totals["placed"],
				"p" => ($totals['placed']) ? number_format((($totals["placed"] / $totals["records"]) * 100), 2) : 0
			);
		}
		if (in_array("ready", $columns)) {
			$return['records']['ready'] = array(
				"r" => $totals["ready"],
				"p" => ($totals['ready']) ? number_format((($totals["ready"] / $totals["records"]) * 100), 2) : 0
			);
		}
		if (in_array("stages", $columns)){
			$return['records']['stages'] = array();
			foreach ($totals['stages'] as $k=>$value){
				$return['records']['stages'][$k] = array(
					"k" => $k,
					"r" => $totals['stages'][$k],
					"p" => ($totals['stages'][$k]) ? number_format((($totals['stages'][$k] / $totals["records"]) * 100), 2) : 0
				);
			}
		}
		
		


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}


}
