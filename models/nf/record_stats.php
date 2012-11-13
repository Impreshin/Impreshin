<?php
/**
 * User: William
 * Date: 2012/05/29 - 2:11 PM
 */
namespace models\nf;

class record_stats {
	public static function stats($where, $columns = array("count"), $pages = "") { // "cm","checked","material","material_approved","layout","placed","placed_cm"
		$timer = new \timer();
		$user = \F3::get("user");

//$columns = array("cm","placed","placed_cm");
		if (is_array($where)) {
			$data = $where;
		} else {
			$data = \models\nf\articles::getAll($where);

		}

		$totals = array(
			"records" => count($data),
		);

		if (in_array("count", $columns)) $totals['count'] = 0;
		if (in_array("stages", $columns)) {
			$stages = stages::getAll();
			$t = array(
				'all'=>array(
					"count"=> $totals['records']
				)
			);
			foreach ($stages as $stage) {
				$stage['count'] = 0;
				$t[$stage['ID']] = $stage;
			}


			$totals['stages'] = $t;
		}




		foreach ($data as $record) {


			if (in_array("count", $columns))  $totals['count'] = $totals['count'] +1;

			if (in_array("stages", $columns))  $totals['stages'][$record['stageID']]['count'] = $totals['stages'][$record['stageID']]['count'] + 1;



		}
		//$totals['dates']= $dIDArray;
		//test_array($dIDArray);




		//test_array($maxPages);
		foreach ($data as $record) {




		}


		$return = array(
			"records" => array(
				"total" => $totals["records"],
			)


		);

		if (in_array("count", $columns)) $return['count'] = $totals['count'];
		if (in_array("stages", $columns)) $return['stages'] = $totals['stages'];



		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}


}
