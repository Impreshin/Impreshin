<?php
/**
 * User: William
 * Date: 2012/05/29 - 2:11 PM
 */
namespace models\ab;
class record_stats {
	public static function stats_cm($where) {

		$timer = new \timer();

		if (is_array($where)) {
			$data = $where;
		} else {
			$data = \models\ab\bookings::getAll($where);
		}

		$totals = array(
			"records"   => count($data),
			"cm"        => 0,

		);


		foreach ($data as $record) {
			if ($record['totalspace']) $totals['cm'] = $totals['cm'] + $record['totalspace'];
		}


		$return = array(
			"cm"     => $totals['cm'],
			"records"=> array(
				"total"    => $totals["records"],
			),


		);


		$timer->stop(array("Models"=> array("Class" => __CLASS__,
		                                    "Method"=> __FUNCTION__
  )
		             ), func_get_args()
		);
		return $return;
	}

	public static function stats_list($where) {
		$timer = new \timer();

		if (is_array($where)) {
			$data = $where;
		} else {
			$data = \models\ab\bookings::getAll($where);
		}
		$totals = array(
			"records" => count($data),
			"cm"      => 0,
			"checked" => 0,
			"material"=> 0,
			"layout"  => 0,

		);

		foreach ($data as $record) {
			if ($record['totalspace']) $totals['cm'] = $totals['cm'] + $record['totalspace'];
			if ($record['checked']) $totals['checked'] = $totals['checked'] + 1;
			if ($record['material']) $totals['material'] = $totals['material'] + 1;
			if ($record['layout']) $totals['layout'] = $totals['layout'] + 1;

		}


		$return = array(
			"cm"     => $totals['cm'],
			"records"=> array(
				"total"   => $totals["records"],
				"checked" => array(
					"r"=> $totals["checked"],
					"p"=> ($totals['records']) ? number_format((($totals["checked"] / $totals["records"]) * 100), 2) : 0
				),
				"material"=> array(
					"r"=> $totals["material"],
					"p"=> ($totals['records']) ? number_format((($totals["material"] / $totals["records"]) * 100), 2) : 0
				),
				"layout"  => array(
					"r"=> $totals["layout"],
					"p"=> ($totals['records']) ? number_format((($totals["layout"] / $totals["records"]) * 100), 2) : 0
				),
			),


		);


		$timer->stop(array("Models"=> array("Class" => __CLASS__,
		                                    "Method"=> __FUNCTION__
  )
		             ), func_get_args()
		);
		return $return;
	}

	public static function stats_production($where) {
		$timer = new \timer();

		if (is_array($where)) {
			$data = $where;
		} else {
			$data = \models\ab\bookings::getAll($where);
		}
		$totals = array(
			"records"           => count($data),
			"cm"                => 0,
			"material_approved" => 0,
			"material"          => 0,

		);

		//test_array($data);

		foreach ($data as $record) {
			if ($record['totalspace']) $totals['cm'] = $totals['cm'] + $record['totalspace'];
			if ($record['material']) $totals['material'] = $totals['material'] + 1;
			if ($record['material_approved']) $totals['material_approved'] = $totals['material_approved'] + 1;

		}


		$return = array(
			"cm"     => $totals['cm'],
			"records"=> array(
				"total"              => $totals["records"],

				"material"           => array(
					"r"=> $totals["material"],
					"p"=> ($totals['records']) ? number_format((($totals["material"] / $totals["records"]) * 100), 2) : 0
				),
				"material_approved"  => array(
					"r"=> $totals["material_approved"],
					"p"=> ($totals['material']) ? number_format((($totals["material_approved"] / $totals["material"]) * 100), 2) : 0
				),
			),


		);


		$timer->stop(array("Models"=> array("Class" => __CLASS__,
		                                    "Method"=> __FUNCTION__
  )
		             ), func_get_args()
		);
		return $return;
	}

	public static function stats_layout($where) {
		$timer = new \timer();

		if (is_array($where)) {
			$data = $where;
		} else {
			$data = \models\ab\bookings::getAll($where);
		}

		$totals = array(
			"records"   => count($data),
			"cm"        => 0,
			"placed"    => 0,
			"placed_cm" => 0,

		);


		$lastdID = "";
		$maxPages = 0;
		foreach ($data as $record) {
			if ($lastdID != $record['dID']) {
				$maxPages = pages::maxPages($record['dID'], $data);
			}
			$lastdID = $record['dID'];
			if ($record['totalspace']) $totals['cm'] = $totals['cm'] + $record['totalspace'];
			if ($record['page'] && ($record['page'] <= $maxPages)) {
				$totals['placed'] = $totals['placed'] + 1;
				$totals['placed_cm'] = $totals['placed_cm'] + $record['totalspace'];
			}

		}


		$return = array(
			"cm"     => $totals['cm'],
			"records"=> array(
				"total"    => $totals["records"],

				"placed_cm"=> $totals['placed_cm'],
				"placed"   => array(
					"r"=> $totals["placed"],
					"p"=> ($totals['records']) ? number_format((($totals["placed"] / $totals["records"]) * 100), 2) : 0
				),

			),


		);


		$timer->stop(array("Models"=> array("Class" => __CLASS__,
		                                    "Method"=> __FUNCTION__
  )
		             ), func_get_args()
		);
		return $return;
	}


}
