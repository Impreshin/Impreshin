<?php
/**
 * User: William
 * Date: 2012/07/11 - 2:23 PM
 */
namespace apps\ab\models;



use \timer as timer;

class report_discounts {
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


		$select = "global_dates.publish_date as publishDate, sum(totalCost) as totalCost, sum(totalShouldbe) as totalShouldbe, sum(totalspace) as totalspace, count(ab_bookings.ID) as records, ab_bookings.pID as pID, global_publications.publication, ab_bookings.dID, typeID";

		$d = bookings::getAll_select($select, $where, "global_dates.publish_date ASC", "ab_bookings.dID, typeID");


		$blank = array(

			"net"     => 0,
			"gross"   => 0,
			"records" => 0,
			"percent" => 0,
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


			$data[$year][$month]['net'] = $data[$year][$month]['net'] + $record['totalCost'];
			$data[$year][$month]['gross'] = $data[$year][$month]['gross'] + $record['totalShouldbe'];
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


			$data[$year][$month]['e'][$edition]['net'] = $data[$year][$month]['e'][$edition]['net'] + $record['totalCost'];
			$data[$year][$month]['e'][$edition]['gross'] = $data[$year][$month]['e'][$edition]['gross'] + $record['totalShouldbe'];
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
			$i_n = 0;
			$i_g = 0;
			$i_r = 0;
			$i_p = 0;
			$totals = $blank;
			$editions = array();

			foreach ($years as $year) {


				$net = isset($data[$year][$month['k']]['net']) ? ($data[$year][$month['k']]['net']) : 0;
				$gross = isset($data[$year][$month['k']]['gross']) ? ($data[$year][$month['k']]['gross']) : 0;
				$records = isset($data[$year][$month['k']]['records']) ? ($data[$year][$month['k']]['records']) : 0;
				$percent = ($net && $gross) ? (($net - $gross) / $gross) * 100 : "";


				$totals['net'] = $totals['net'] + $net;
				$totals['gross'] = $totals['gross'] + $gross;
				$totals['records'] = $totals['records'] + $records;
				$totals['percent'] = $totals['percent'] + $percent;


				if (isset($data[$year][$month['k']]['net'])) {
					$i_n++;
				}
				if (isset($data[$year][$month['k']]['gross'])) {
					$i_g++;
				}
				if (isset($data[$year][$month['k']]['records'])) {
					$i_r++;
				}
				if (isset($data[$year][$month['k']]['net']) && isset($data[$year][$month['k']]['gross'])) {
					$i_p++;
				}


				//	test_array($totals);


				$r['data'][] = array(
					"year"    => $year,
					"net"     => ($net) ? ($net) : "",
					"gross"   => ($gross) ? $gross : "",
					"records" => ($records) ? $records : "",
					"percent" => ($net && $gross) ? (($net - $gross) / $gross) * 100 : "",
					"d"       => array(
						"net"     => "",
						"gross"   => "",
						"records" => "",
						"percent" => ""
					),

				);


				$editions_d = isset($data[$year][$month['k']]['e']) ? ($data[$year][$month['k']]['e']) : array();

				//$editions[]['data'] = array();
				foreach ($editions_d as $e) {
					$n = array(
						"dID"  => $e['dID'],
						"date" => $e['date'],
						"key"  => date("Y|m", strtotime($e['date'])),
						"pub"  => $e['pub'],

					);

					unset($e['pub']);
					unset($e['dID']);
					foreach ($years as $year) {
						if ($year == date("Y", strtotime($e['date']))) {
							$e['percent'] = ($e['net'] && $e['gross']) ? abs(number_format((($e['net'] - $e['gross']) / $e['gross']) * 100, 1)) : "";
							$e['net'] = currency($e['net']);
							$e['gross'] = currency($e['gross']);

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


			$r['editions'] = $editions;
			$r['averages']['net'] = ($i_n) ? $totals['net'] / $i_n : $totals['net'];
			$r['averages']['gross'] = ($i_g) ? $totals['gross'] / $i_g : $totals['gross'];
			$r['averages']['records'] = ($i_r) ? $totals['records'] / $i_r : $totals['records'];
			$r['averages']['percent'] = ($i_r) ? $totals['percent'] / $i_r : $totals['percent'];


			$ndata = array();
			foreach ($r['data'] as $rec) {

				$col = "percent";
				if (abs($rec[$col]) > $margin) {
					$rec['d'][$col] = "d";
				}
				//	test_array($rec);


				$rec['net'] = ($rec['net']) ? currency($rec['net']) : "";
				$rec['gross'] = ($rec['gross']) ? currency($rec['gross']) : "";
				$rec['percent'] = ($rec['percent']) ? abs(number_format(($rec['percent']), 1)) : "";

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


		$select = "global_dates.publish_date as publishDate, sum(totalCost) as totalCost, sum(totalShouldbe) as totalShouldbe, sum(totalspace) as totalspace, count(ab_bookings.ID) as records, ab_bookings.pID as pID, global_publications.publication, ab_bookings.dID, typeID";

		$d = bookings::getAll_select($select, $where, "global_dates.publish_date ASC", "ab_bookings.dID");


		$publications = \models\publications::getAll("global_publications.ID in ($publications_where)");


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
			"net"     => 0,
			"gross"   => 0,
			"percent" => 0,
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


			$data[$k]['totals'] = $data[$k]['totals'] + abs($record['totalShouldbe'] - $record['totalCost']);
			$data[$k]['net'] = $data[$k]['net'] + ($record['totalCost']);
			$data[$k]['gross'] = $data[$k]['gross'] + ($record['totalShouldbe']);
			$data[$k]['percent'] = ($data[$k]['net'] && $data[$k]['gross']) ? abs(number_format((($data[$k]['net'] - $data[$k]['gross']) / $data[$k]['gross']) * 100, 2)) : "";
			$data[$k]['records'] = $data[$k]['records'] + $record['records'];

			$data[$k]['pubs'][$record['pID']]['totals'] = $data[$k]['pubs'][$record['pID']]['totals'] + abs($record['totalShouldbe'] - $record['totalCost']);
			$data[$k]['pubs'][$record['pID']]['net'] = $data[$k]['pubs'][$record['pID']]['net'] + ($record['totalCost']);
			$data[$k]['pubs'][$record['pID']]['gross'] = $data[$k]['pubs'][$record['pID']]['gross'] + ($record['totalShouldbe']);
			$data[$k]['pubs'][$record['pID']]['percent'] = ($data[$k]['pubs'][$record['pID']]['net'] && $data[$k]['pubs'][$record['pID']]['gross']) ? abs(number_format((($data[$k]['pubs'][$record['pID']]['net'] - $data[$k]['pubs'][$record['pID']]['gross']) / $data[$k]['pubs'][$record['pID']]['gross']) * 100, 2)) : "";
			$data[$k]['pubs'][$record['pID']]['records'] = $data[$k]['pubs'][$record['pID']]['records'] + $record['records'];
		}

		$p = array();
		$data_ret = array(
			"labels"   => array(),
			"labels_d" => array(),
			"totals"   => array(),
			"percent"  => array(),
			"records"  => array(),
			"pubs"     => array()
		);
		foreach ($data as $d) {
			$data_ret['labels'][] = $d['label'];
			$data_ret['labels_d'][] = $d['label_d'];
			$data_ret['totals'][] = ($d['totals']) ? $d['totals'] : 0;
			$data_ret['percent'][] = $d['percent'];
			$data_ret['records'][] = $d['records'];
			foreach ($publications as $pub) {

				$data_ret['pubs'][$pub['ID']]['pub'] = $pub['publication'];
				$data_ret['pubs'][$pub['ID']]['totals'][] = ($d['pubs'][$pub['ID']]['totals']) ? $d['pubs'][$pub['ID']]['totals'] : null;
				$data_ret['pubs'][$pub['ID']]['percent'][] = ($d['pubs'][$pub['ID']]['percent']) ? $d['pubs'][$pub['ID']]['percent'] : null;
				$data_ret['pubs'][$pub['ID']]['records'][] = ($d['pubs'][$pub['ID']]['records']) ? $d['pubs'][$pub['ID']]['records'] : null;


			}
		}
		$p = array();
		foreach ($data_ret['pubs'] as $record) {
			$p[] = $record;
		}
		$data_ret['pubs'] = $p;


		//test_array($data_ret);


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $data_ret;
	}

}
