<?php
/**
 * User: William
 * Date: 2012/07/11 - 2:23 PM
 */
namespace models\ab;

use \F3 as F3;
use \Axon as Axon;
use \timer as timer;

class report_figures {
	public static function figures($where, $years, $margin = "25") {
		$timer = new timer();
		$f3 = \Base::instance();
		$return = array();
		$months = array(
			array(
				"h" => "January",
				"k" => "01"
			),
			array(
				"h" => "February",
				"k" => "02"
			),
			array(
				"h" => "March",
				"k" => "03"
			),
			array(
				"h" => "April",
				"k" => "04"
			),
			array(
				"h" => "May",
				"k" => "05"
			),
			array(
				"h" => "June",
				"k" => "06"
			),
			array(
				"h" => "July",
				"k" => "07"
			),
			array(
				"h" => "August",
				"k" => "08"
			),
			array(
				"h" => "September",
				"k" => "09"
			),
			array(
				"h" => "October",
				"k" => "10"
			),
			array(
				"h" => "November",
				"k" => "11"
			),
			array(
				"h" => "December",
				"k" => "12"
			)
		);


		$select = "global_dates.publish_date as publishDate, sum(totalCost) as totalCost, sum(totalspace) as totalspace, count(ab_bookings.ID) as records, ab_bookings.pID as pID, global_publications.publication, ab_bookings.dID, ab_bookings.typeID";

		$d = bookings::getAll_select($select, $where, "global_dates.publish_date ASC", "ab_bookings.dID");


		


		$blank = array(
			"totals"  => array(
				"total" => 0,
				"type"  => array()
			),
			"cm"      => 0,
			"records" => 0,
			"yield"   => 0,
		);


		$data = array();
		$editions = array();
		foreach ($d as $record) {
			$year = date("Y", strtotime($record['publishDate']));
			$month = date("m", strtotime($record['publishDate']));
			if (!isset($data[$year])) {
				$data[$year] = array();
			}
			if (!isset($data[$year][$month])) {
				$data[$year][$month] = $blank;
				$data[$year][$month]['e'] = array();
			}


			if (!isset($data[$year][$month]['totals']['type']["t_" . $record['typeID']])) $data[$year][$month]['totals']['type']["t_" . $record['typeID']] = 0;

			$data[$year][$month]['totals']['total'] = $data[$year][$month]['totals']['total'] + $record['totalCost'];
			$data[$year][$month]['totals']['type']["t_" . $record['typeID']] = $data[$year][$month]['totals']['type']["t_" . $record['typeID']] + $record['totalCost'];


			$data[$year][$month]['cm'] = $data[$year][$month]['cm'] + $record['totalspace'];
			$data[$year][$month]['records'] = $data[$year][$month]['records'] + $record['records'];


			$edition = $record['dID'];
			if (!isset($data[$year][$month]['e'][$edition])) {
				$data[$year][$month]['e'][$edition] = $blank;
			}

			if (!isset($data[$year][$month]['e'][$edition]['date'])) {
				$data[$year][$month]['e'][$edition]['date'] = date("Y-m-d", strtotime($record['publishDate']));
				$data[$year][$month]['e'][$edition]['pub'] = $record['publication'];
				$data[$year][$month]['e'][$edition]['dID'] = $record['dID'];
			}

			if (!isset($data[$year][$month]['e'][$edition]['totals']['type']["t_" . $record['typeID']])) $data[$year][$month]['e'][$edition]['totals']['type']["t_" . $record['typeID']] = 0;


			$data[$year][$month]['e'][$edition]['totals']['total'] = $data[$year][$month]['e'][$edition]['totals']['total'] + $record['totalCost'];
			$data[$year][$month]['e'][$edition]['totals']['type']["t_" . $record['typeID']] = $data[$year][$month]['e'][$edition]['totals']['type']["t_" . $record['typeID']] + $record['totalCost'];

			$data[$year][$month]['e'][$edition]['cm'] = $data[$year][$month]['e'][$edition]['cm'] + $record['totalspace'];
			$data[$year][$month]['e'][$edition]['records'] = $data[$year][$month]['e'][$edition]['records'] + $record['records'];


		}


		$ret = array();
		foreach ($months as $month) {
			$r = array(
				"month"    => $month['h'],
				"m"        => $month['k'],
				"data"     => array(),
				"averages" => $blank
			);
			$i_t = 0;
			$i_c = 0;
			$i_r = 0;
			$i_y = 0;
			$totals = $blank;
			$editions = array();

			foreach ($years as $year) {


				$total = isset($data[$year][$month['k']]['totals']) ? ($data[$year][$month['k']]['totals']) : array("total" => 0);

				$cm = isset($data[$year][$month['k']]['cm']) ? ($data[$year][$month['k']]['cm']) : 0;
				$records = isset($data[$year][$month['k']]['records']) ? ($data[$year][$month['k']]['records']) : 0;


				$totals['totals']['total'] = $totals['totals']['total'] + $total['total'];


				if (isset($total['type'])) {
					foreach ($total['type'] as $type_k => $type_v) {
						if (!isset($totals['totals']['type'][$type_k])) {
							$totals['totals']['type'][$type_k] = 0;
						}
						if (!isset($total['type'][$type_k])) {
							$total['type'][$type_k] = 0;
						}
						$totals['totals']['type'][$type_k] = $totals['totals']['type'][$type_k] + $total['type'][$type_k];
					}
				}


				$totals['cm'] = $totals['cm'] + $cm;
				$totals['records'] = $totals['records'] + $records;

				if (isset($data[$year][$month['k']]['totals']['total'])) {
					$i_t++;
				}
				if (isset($data[$year][$month['k']]['cm'])) {
					$i_c++;
				}
				if (isset($data[$year][$month['k']]['records'])) {
					$i_r++;
				}
				if (isset($data[$year][$month['k']]['totals']['type']['t_1']) && isset($data[$year][$month['k']]['cm'])) {
					$i_y++;
				}


				//	test_array($totals);


				$r['data'][] = array(
					"year"    => $year,
					"totals"  => ($total) ? ($total) : array("total" => ""),
					"cm"      => ($cm) ? $cm : "",
					"yield"   => ($cm && $total['type']['t_1']) ? $total['type']['t_1'] / $cm : "",
					"records" => ($records) ? $records : "",
					"d"       => array(
						"totals"  => "",
						"cm"      => "",
						"yield"   => "",
						"records" => ""
					),

				);


				$editions_d = isset($data[$year][$month['k']]['e']) ? ($data[$year][$month['k']]['e']) : array();

				//$editions[]['data'] = array();
				foreach ($editions_d as $e) {
					$n = array(
						"date" => $e['date'],
						"key"  => date("Y|m", strtotime($e['date'])),
						"pub"  => $e['pub'],
						"dID"  => $e['dID'],

					);

					unset($e['pub']);
					unset($e['dID']);
					foreach ($years as $year) {
						if ($year == date("Y", strtotime($e['date']))) {
							$e['yield'] = (isset($e['totals']['type']['t_1']) && $e['totals']['type']['t_1'] && $e['cm']) ? currency($e['totals']['type']['t_1'] / $e['cm']) : "";
							$e['totals'] = currency($e['totals']['total']);
							$n['data'][$year] = $e;
						} else {
							$n['data'][$year] = array();
						}

					}

					$hmm = array();
					foreach ($n['data'] as $tt) {
						$hmm[] = $tt;
					}
					$n['data'] = $hmm;

					$editions[] = $n;
				}


			}
			//test_array($editions);
			//test_array($r);


			//test_array($totals);

			$averages = array();
			$averages['total'] = ($i_t) ? $totals['totals']['total'] / $i_t : $totals['totals']['total'];

			if (!isset($totals['totals']['type'])) {
				$averages['type'] = array();
			} else {
				foreach ($totals['totals']['type'] as $type_k => $type_v) {
					$averages['type'][$type_k] = ($i_t) ? $totals['totals']['type'][$type_k] / $i_t : $totals['totals']['type'][$type_k];
				}
			}


			$r['editions'] = $editions;
			$r['averages']['totals'] = $averages;
			$r['averages']['cm'] = ($i_c) ? $totals['cm'] / $i_c : $totals['cm'];
			$r['averages']['records'] = ($i_r) ? $totals['records'] / $i_r : $totals['records'];


			$r['averages']['yield'] = (isset($averages['type']['t_1']) && $averages['type']['t_1'] && $r['averages']['cm']) ? $averages['type']['t_1'] / $r['averages']['cm'] : 0;


			//test_array($r);

			$ndata = array();
			foreach ($r['data'] as $rec) {

				//$rec['yield'] =($rec['yield_totals'] && $rec['cm']) ? ($rec['yield_totals'] / $rec['cm']) : "";
				$col = "totals";

				$figs_c_totals = array(
					$r['averages'][$col]['total'] + ($r['averages'][$col]['total'] * ($margin / 100)),
					$r['averages'][$col]['total'] - ($r['averages'][$col]['total'] * ($margin / 100)),
				);
				if ($rec[$col]['total'] > $figs_c_totals[0]) {
					$rec['d'][$col] = "u";
				} else if ($rec[$col]['total'] < $figs_c_totals[1] && $rec[$col]['total']) {
					$rec['d'][$col] = "d";
				}


				$col = "cm";
				$figs_c_totals = array(
					$r['averages'][$col] + ($r['averages'][$col] * ($margin / 100)),
					$r['averages'][$col] - ($r['averages'][$col] * ($margin / 100)),
				);
				if ($rec[$col] > $figs_c_totals[0]) {
					$rec['d'][$col] = "u";
				} else if ($rec[$col] < $figs_c_totals[1] && $rec[$col]) {
					$rec['d'][$col] = "d";
				}
				$col = "records";
				$figs_c_totals = array(
					$r['averages'][$col] + ($r['averages'][$col] * ($margin / 100)),
					$r['averages'][$col] - ($r['averages'][$col] * ($margin / 100)),
				);
				if ($rec[$col] > $figs_c_totals[0]) {
					$rec['d'][$col] = "u";
				} else if ($rec[$col] < $figs_c_totals[1] && $rec[$col]) {
					$rec['d'][$col] = "d";
				}


				$col = "yield";
				$figs_c_totals = array(
					$r['averages'][$col] + ($r['averages'][$col] * ($margin / 100)),
					$r['averages'][$col] - ($r['averages'][$col] * ($margin / 100)),
				);
				if ($rec[$col] > $figs_c_totals[0]) {
					$rec['d'][$col] = "u";
				} else if ($rec[$col] < $figs_c_totals[1] && $rec[$col]) {
					$rec['d'][$col] = "d";
				}


				$rec['totals']['total'] = ($rec['totals']['total']) ? currency($rec['totals']['total']) : "";
				$rec['yield'] = ($rec['yield']) ? currency($rec['yield']) : "";

				$ndata[] = $rec;

			}
			$r['data'] = $ndata;

			$ret[] = $r;
		}


		$return = $ret;

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function lines($where, $dates = array("from" => "", "to" => ""), $publications) {
		$timer = new timer();
		$f3 = \Base::instance();
		$return = array();
		if (is_array($publications)) {
			$publications_where = implode(",", $publications);

		} else {
			$publications_where = $publications;
			$publications = explode(",", $publications);
		}


		$date1 = strtotime($dates['from']);
		$date2 = strtotime($dates['to']);

		$from = date("Y-m-d", $date1);
		$to = date("Y-m-d", $date2);

		if ($where) {
			$where = $where . " AND ";
		}
		$where = $where . "(ab_bookings.pID in ($publications_where)  AND (global_dates.publish_date>='$from' AND global_dates.publish_date<='$to'))";


		$select = "global_dates.publish_date as publishDate, totalCost, totalspace, ab_bookings.pID as pID";


		$select = "global_dates.publish_date as publishDate, sum(totalCost) as totalCost, sum(totalspace) as totalspace, count(ab_bookings.ID) as records, ab_bookings.pID as pID, global_publications.publication, ab_bookings.dID, typeID";

		$d = bookings::getAll_select($select, $where, "global_dates.publish_date ASC", "ab_bookings.dID");


		$publications = \models\publications::getAll("ID in ($publications_where)");


		$data = self::line_build_data($d, $date1, $date2, $publications);


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $data;
	}

	private static function line_build_data($records, $date1, $date2, $publications) {
		$timer = new timer();
		$f3 = \Base::instance();
		$return = array();

		$blank = array(
			"label"   => "",
			"label_d" => "",
			"totals"  => 0,
			"cm"      => 0,
			"records" => 0
		);

		$i = 0;
		$data = array();

		while ($date1 <= $date2) {
			$k = date('mY', $date1);
			$h = date('M', $date1);
			$tooltip_label = date('F Y', $date1);
			if (in_array($h, array("Jan"))) {
				$h = date('M \'y', $date1);
			}
			$data[$k] = $blank;
			$data[$k]['label'] = $h;
			$data[$k]['label_d'] = $tooltip_label;

			foreach ($publications as $pub) {
				$data[$k]['pubs'][$pub['ID']] = $blank;
				$data[$k]['pubs'][$pub['ID']]['label'] = $pub['publication'];

			}


			$date1 = strtotime('+1 month', $date1);
		}


		$labels = array();

		foreach ($records as $record) {

			$k = date("mY", strtotime($record['publishDate']));


			$data[$k]['totals'] = $data[$k]['totals'] + $record['totalCost'];
			$data[$k]['cm'] = $data[$k]['cm'] + $record['totalspace'];
			$data[$k]['records'] = $data[$k]['records'] + $record['records'];

			$data[$k]['pubs'][$record['pID']]['totals'] = $data[$k]['pubs'][$record['pID']]['totals'] + $record['totalCost'];
			$data[$k]['pubs'][$record['pID']]['cm'] = $data[$k]['pubs'][$record['pID']]['cm'] + $record['totalspace'];
			$data[$k]['pubs'][$record['pID']]['records'] = $data[$k]['pubs'][$record['pID']]['records'] + $record['records'];

		}

		$p = array();
		$data_ret = array(
			"labels"   => array(),
			"labels_d" => array(),
			"totals"   => array(),
			"cm"       => array(),
			"records"  => array(),
			"pubs"     => array()
		);
		foreach ($data as $d) {
			$data_ret['labels'][] = $d['label'];
			$data_ret['labels_d'][] = $d['label_d'];
			$data_ret['totals'][] = ($d['totals']) ? $d['totals'] : 0;
			$data_ret['cm'][] = $d['cm'];
			$data_ret['records'][] = $d['records'];
			foreach ($publications as $pub) {

				$data_ret['pubs'][$pub['ID']]['pub'] = $pub['publication'];
				$data_ret['pubs'][$pub['ID']]['totals'][] = ($d['pubs'][$pub['ID']]['totals']) ? $d['pubs'][$pub['ID']]['totals'] : null;
				$data_ret['pubs'][$pub['ID']]['cm'][] = ($d['pubs'][$pub['ID']]['cm']) ? $d['pubs'][$pub['ID']]['cm'] : null;
				$data_ret['pubs'][$pub['ID']]['records'][] = ($d['pubs'][$pub['ID']]['records']) ? $d['pubs'][$pub['ID']]['records'] : null;


			}
		}
		$p = array();
		foreach ($data_ret['pubs'] as $record) {
			$p[] = $record;
		}
		$data_ret['pubs'] = $p;


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $data_ret;
	}

}
