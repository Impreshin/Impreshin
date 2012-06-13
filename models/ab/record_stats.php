<?php
/**
 * User: William
 * Date: 2012/05/29 - 2:11 PM
 */
namespace models\ab;
class record_stats {
	public static function stats($where,$columns=array("cm"),$pages=""){ // "cm","checked","material","material_approved","layout","placed","placed_cm"
		$timer = new \timer();
		$totals = array();
//$columns = array("cm","placed","placed_cm");
		if (is_array($where)) {
			$data = $where;
		} else {
			$data = \models\ab\bookings::getAll($where);

		}

			$totals = array(
				"records"   => count($data),
			);

			if (in_array("cm",$columns)) $totals['cm'] = 0;
			if (in_array("checked",$columns)) $totals['checked'] = 0;
			if (in_array("material",$columns)) $totals['material'] = 0;
			if (in_array("material_approved",$columns)) $totals['material_approved'] = 0;
			if (in_array("layout",$columns)) $totals['layout'] = 0;
			if (in_array("placed",$columns)) $totals['placed'] = 0;
			if (in_array("placed_cm",$columns)) $totals['placed_cm'] = 0;

			$lastdID_ = "";
			$maxPages = 0;

		$dIDArray = array();
			foreach ($data as $record) {
				if (!array_key_exists($record['dID'], $dIDArray)) {
					$dIDArray[$record['dID']] = array("dID"=> $record['dID'],"cm"=>0);
				}
				$dIDArray[$record['dID']]['cm'] = $dIDArray[$record['dID']]['cm'] + $record['totalspace'];

				if (in_array("cm", $columns)) if ($record['totalspace']) $totals['cm'] = $totals['cm'] + $record['totalspace'];
				if (in_array("checked", $columns)) if ($record['checked']) $totals['checked'] = $totals['checked'] + 1;
				if (in_array("material", $columns)) if ($record['material']) $totals['material'] = $totals['material'] + 1;
				if (in_array("material_approved", $columns)) if ($record['material_approved']) $totals['material_approved'] = $totals['material_approved'] + 1;
				if (in_array("layout", $columns)) if ($record['layout']) $totals['layout'] = $totals['layout'] + 1;



			}
		//$totals['dates']= $dIDArray;
		//test_array($dIDArray);



		$maxPages = array();
			foreach ($dIDArray as $d) {
				$maxPages[$d['dID']] = ($pages)? $pages: pages::maxPages($d['dID'], $d['cm']);

			}

		//test_array($maxPages);
		foreach ($data as $record) {

				if (in_array("placed", $columns) || in_array("placed_cm", $columns) && $record['page']) {
					$maxPage = $maxPages[$record['dID']];

					if ($maxPage && $record['page'] && ($record['page'] > $maxPage)) {

					} else {
						if (in_array("placed", $columns)) if ($record['page']) $totals['placed'] = $totals['placed'] + 1;
						if (in_array("placed_cm", $columns)) if ($record['page']) $totals['placed_cm'] = $totals['placed_cm'] + $record['totalspace'];
					}


				}


			}


			$return = array(
				"records"=> array(
					"total"    => $totals["records"],
				)


			);

		if (in_array("cm", $columns)) $return['cm'] = $totals['cm'] ;

			if (in_array("checked", $columns)) $return['records']['checked'] = array(
				"r"=> $totals["checked"],
				"p"=> ($totals['records']) ? number_format((($totals["checked"] / $totals["records"]) * 100), 2) : 0
			);
			if (in_array("material", $columns)) $return['records']['material'] = array(
				"r"=> $totals["material"],
				"p"=> ($totals['records']) ? number_format((($totals["material"] / $totals["records"]) * 100), 2) : 0
			);
			if (in_array("material_approved", $columns)) {
				$t = (isset($totals["material"])) ? $totals["material"] : $totals["records"];
				$return['records']['material_approved'] = array(
					"r"=> $totals["material_approved"],
					"p"=> ($t) ? number_format((($totals["material_approved"] / $t) * 100), 2) : 0
				);
			}

			if (in_array("layout", $columns)) $return['records']['layout'] = array(
				"r"=> $totals["layout"],
				"p"=> ($totals['records']) ? number_format((($totals["layout"] / $totals["records"]) * 100), 2) : 0
			);



			if (in_array("placed", $columns)) $return['records']['placed'] = array(
				"r"=> $totals["placed"],
				"p"=> ($totals['records']) ? number_format((($totals["placed"] / $totals["records"]) * 100), 2) : 0
			);
			if (in_array("placed_cm", $columns)) $return['records']['placed_cm'] = $totals['placed_cm'];







		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}





}
