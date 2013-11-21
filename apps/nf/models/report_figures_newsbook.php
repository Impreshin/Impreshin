<?php
/**
 * User: William
 * Date: 2012/07/11 - 2:23 PM
 */
namespace apps\nf\models;



use \timer as timer;

class report_figures_newsbook {
	public static function figures($where, $years, $pID,  $margin = "25") {
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

		//$where = $where . " AND nf_articles.ID='17491'";

		$d = articles::getAll($where, "","",array("pID"=>$pID,"distinct"=>"nf_articles.ID, nf_article_newsbook.pID, nf_article_newsbook.dID","select"=>" nf_article_newsbook.ID AS newsbookID, nf_article_newsbook.pID, global_dates.publish_date, global_publications.publication"));


	//test_array($d);


		$blank = array(
			"cm"=>0,
			"articlesCount"=>0,
			"photosCount"=>0,
			"filesCount"=>0,
			"percentChanged"=>0,
			"records" => 0,
			"types"=>array(

			)

		);

		$data = array();
		$editions = array();
		foreach ($d as $record) {
			

			
			$year = date("Y", strtotime($record['publish_date']));
			$month = date("m", strtotime($record['publish_date']));
			if (!isset($data[$year])) {
				$data[$year] = array();
			}
			if (!isset($data[$year][$month])) {
				$data[$year][$month] = $blank;
				$data[$year][$month]['e'] = array();
			}


			if (!isset($data[$year][$month]['types']["t_" . $record['typeID']])) $data[$year][$month]['types']["t_" . $record['typeID']] = 0;

			if ($record['typeID']=='1') {
				$data[$year][$month]['articlesCount'] = $data[$year][$month]['articlesCount'] + 1;
				$data[$year][$month]['percentChanged'] = $data[$year][$month]['percentChanged'] + $record['percent_orig'];
			}

			$data[$year][$month]['cm'] = $data[$year][$month]['cm'] + $record['cm'];
			$data[$year][$month]['photosCount'] = $data[$year][$month]['photosCount'] + $record['photosCount'];
			$data[$year][$month]['filesCount'] = $data[$year][$month]['filesCount'] + $record['filesCount'];


			$data[$year][$month]['records'] = $data[$year][$month]['records'] + 1;
			$data[$year][$month]['types']["t_" . $record['typeID']] = $data[$year][$month]['types']["t_" . $record['typeID']] + 1;


			$edition = $record['dID'];
			if (!isset($data[$year][$month]['e'][$edition])) {
				$data[$year][$month]['e'][$edition] = $blank;
			}
			if (!isset($data[$year][$month]['e'][$edition]['date'])) {
				$data[$year][$month]['e'][$edition]['date'] = date("Y-m-d", strtotime($record['publish_date']));
				$data[$year][$month]['e'][$edition]['pub'] = $record['publication'];
				$data[$year][$month]['e'][$edition]['dID'] = $record['dID'];
			}
			
			
			if (!isset($data[$year][$month]['e'][$edition]['types']["t_" . $record['typeID']])) $data[$year][$month]['e'][$edition]['types']["t_" . $record['typeID']] = 0;

			if ($record['typeID']=='1') {
				$data[$year][$month]['e'][$edition]['articlesCount'] = $data[$year][$month]['e'][$edition]['articlesCount'] + 1;
				$data[$year][$month]['e'][$edition]['percentChanged'] = $data[$year][$month]['e'][$edition]['percentChanged'] + $record['percent_orig'];
			}

			$data[$year][$month]['e'][$edition]['cm'] = $data[$year][$month]['e'][$edition]['cm'] + $record['cm'];
			$data[$year][$month]['e'][$edition]['photosCount'] = $data[$year][$month]['e'][$edition]['photosCount'] + $record['photosCount'];
			$data[$year][$month]['e'][$edition]['filesCount'] = $data[$year][$month]['e'][$edition]['filesCount'] + $record['filesCount'];


			$data[$year][$month]['e'][$edition]['records'] = $data[$year][$month]['e'][$edition]['records'] + 1;
			$data[$year][$month]['e'][$edition]['types']["t_" . $record['typeID']] = $data[$year][$month]['e'][$edition]['types']["t_" . $record['typeID']] + 1;
			
			
			

		}
		
		


		$ret = array();
		foreach ($months as $month) {
			$r = array(
				"month"    => $month['h'],
				"m"        => $month['k'],
				"data"     => array(),
				"averages" => $blank
			);
			$i_cm = 0;
			$i_records = 0;
			$i_articlesCount = 0;
			$i_photosCount = 0;
			$i_filesCount = 0;
			$i_percentChanged = 0;
			$totals = $blank;
			$editions = array();

			foreach ($years as $year) {

				$cm = isset($data[$year][$month['k']]['cm']) ? ($data[$year][$month['k']]['cm']) : 0;
				$records = isset($data[$year][$month['k']]['records']) ? ($data[$year][$month['k']]['records']) : 0;
				$photosCount = isset($data[$year][$month['k']]['photosCount']) ? ($data[$year][$month['k']]['photosCount']) : 0;
				$filesCount = isset($data[$year][$month['k']]['filesCount']) ? ($data[$year][$month['k']]['filesCount']) : 0;



				$articlesCount = isset($data[$year][$month['k']]['articlesCount']) ? ($data[$year][$month['k']]['articlesCount']) : 0;

				$percentChanged = isset($data[$year][$month['k']]['percentChanged']) ? ($data[$year][$month['k']]['percentChanged']) : 0;


				if ($articlesCount > 0 && $percentChanged > 0) $percentChanged = $articlesCount / $percentChanged;


				$totals['cm'] = $totals['cm'] + $cm;
				$totals['records'] = $totals['records'] + $records;
				$totals['photosCount'] = $totals['photosCount'] + $photosCount;
				$totals['filesCount'] = $totals['filesCount'] + $filesCount;
				$totals['articlesCount'] = $totals['articlesCount'] + $articlesCount;
				$totals['percentChanged'] = $totals['percentChanged'] + $percentChanged;

				if (isset($data[$year][$month['k']]['articlesCount'])) {
					$i_articlesCount++;
				}
				if (isset($data[$year][$month['k']]['photosCount'])) {
					$i_photosCount++;
				}
				if (isset($data[$year][$month['k']]['filesCount'])) {
					$i_filesCount++;
				}
				if (isset($data[$year][$month['k']]['cm'])) {
					$i_cm++;
				}
				if (isset($data[$year][$month['k']]['records'])) {
					$i_records++;
				}
				if (isset($data[$year][$month['k']]['percentChanged'])) {
					$i_percentChanged++;
				}


				//	test_array($totals);


				$r['data'][] = array(
					"year"    => $year,

					"cm"      => ($cm) ? $cm : "",
					"photosCount"      => ($photosCount) ? $photosCount : "",
					"filesCount"      => ($filesCount) ? $filesCount : "",
					"articlesCount"      => ($articlesCount) ? $articlesCount : "",
					"percentChanged"      => ($percentChanged) ? $percentChanged : "",

					"records" => ($records) ? $records : "",
					"d"       => array(

						"cm"      => "",
						"photosCount"      => "",
						"filesCount"      => "",
						"articlesCount"      => "",

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
			$r['editions'] = $editions;

			$r['averages']['cm'] = ($i_cm) ? $totals['cm'] / $i_cm : $totals['cm'];
			$r['averages']['records'] = ($i_records) ? $totals['records'] / $i_records : $totals['records'];
			$r['averages']['photosCount'] = ($i_photosCount) ? $totals['photosCount'] / $i_photosCount : $totals['photosCount'];
			$r['averages']['filesCount'] = ($i_filesCount) ? $totals['filesCount'] / $i_filesCount : $totals['filesCount'];
			$r['averages']['articlesCount'] = ($i_articlesCount) ? $totals['articlesCount'] / $i_articlesCount : $totals['articlesCount'];

			$r['averages']['percentChanged'] = ($i_percentChanged) ? $totals['percentChanged'] / $i_percentChanged : $totals['percentChanged'];


		//	test_array($r);

			$ndata = array();
			foreach ($r['data'] as $rec) {

				//$rec['yield'] =($rec['yield_totals'] && $rec['cm']) ? ($rec['yield_totals'] / $rec['cm']) : "";



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


				$col = "photosCount";
				$figs_c_totals = array(
					$r['averages'][$col] + ($r['averages'][$col] * ($margin / 100)),
					$r['averages'][$col] - ($r['averages'][$col] * ($margin / 100)),
				);
				if ($rec[$col] > $figs_c_totals[0]) {
					$rec['d'][$col] = "u";
				} else if ($rec[$col] < $figs_c_totals[1] && $rec[$col]) {
					$rec['d'][$col] = "d";
				}

				$col = "filesCount";
				$figs_c_totals = array(
					$r['averages'][$col] + ($r['averages'][$col] * ($margin / 100)),
					$r['averages'][$col] - ($r['averages'][$col] * ($margin / 100)),
				);
				if ($rec[$col] > $figs_c_totals[0]) {
					$rec['d'][$col] = "u";
				} else if ($rec[$col] < $figs_c_totals[1] && $rec[$col]) {
					$rec['d'][$col] = "d";
				}

				$col = "articlesCount";
				$figs_c_totals = array(
					$r['averages'][$col] + ($r['averages'][$col] * ($margin / 100)),
					$r['averages'][$col] - ($r['averages'][$col] * ($margin / 100)),
				);
				if ($rec[$col] > $figs_c_totals[0]) {
					$rec['d'][$col] = "u";
				} else if ($rec[$col] < $figs_c_totals[1] && $rec[$col]) {
					$rec['d'][$col] = "d";
				}
				$col = "percentChanged";
				$figs_c_totals = array(
					$r['averages'][$col] + ($r['averages'][$col] * ($margin / 100)),
					$r['averages'][$col] - ($r['averages'][$col] * ($margin / 100)),
				);
				if ($rec[$col] > $figs_c_totals[0]) {
					$rec['d'][$col] = "u";
				} else if ($rec[$col] < $figs_c_totals[1] && $rec[$col]) {
					$rec['d'][$col] = "d";
				}







				$ndata[] = $rec;

			}
			$r['data'] = $ndata;

			$ret[] = $r;
		}


		$return = $ret;

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function lines($where, $dates = array("from" => "", "to" => "")) {
		$timer = new timer();
		$f3 = \Base::instance();
		$return = array();

		$user = $f3->get("user");
		$cID = $user['company']['ID'];
		$pID = $user['publication']['ID'];


		$date1 = strtotime($dates['from']);
		$date2 = strtotime($dates['to']);

		$from = date("Y-m-d", $date1);
		$to = date("Y-m-d", $date2);

		if ($where) {
			$where = $where . " AND ";
		}
		$where = $where . "(nf_articles.cID = '$cID'   AND (global_dates.publish_date>='$from' AND global_dates.publish_date<='$to'))";




		$d = articles::getAll($where, "","",array("pID"=>$pID,"distinct"=>"nf_articles.ID, nf_article_newsbook.pID, nf_article_newsbook.dID","select"=>"  nf_article_newsbook.ID AS newsbookID, global_dates.publish_date, global_publications.publication"));


		//$d = articles::getAll($where, "nf_articles.ID ASC");




		$data = self::line_build_data($d, $date1, $date2);

		//$data = array();

		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $data;
	}

	private static function line_build_data($records, $date1, $date2) {
		$timer = new timer();
		$f3 = \Base::instance();
		$return = array();

		$blank = array(
			"label"   => "",
			"label_d" => "",
			"articlesCount"  => 0,
			"photosCount"  => 0,
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


			$date1 = strtotime('+1 month', $date1);
		}




		$labels = array();

		foreach ($records as $record) {

			$k = date("mY", strtotime($record['publish_date']));


			if ($record['typeID']=='1') {
				$data[$k]['articlesCount'] = $data[$k]['articlesCount'] + 1;
			}
			$data[$k]['photosCount'] = $data[$k]['photosCount'] + $record['photosCount'];
			$data[$k]['cm'] = $data[$k]['cm'] + $record['cm'];
			$data[$k]['records'] = $data[$k]['records'] + 1;



		}



		$p = array();
		$data_ret = array(
			"labels"   => array(),
			"labels_d" => array(),
			"articlesCount"   => array(),
			"photosCount"   => array(),
			"cm"       => array(),
			"records"  => array(),

		);
		foreach ($data as $d) {
			$data_ret['labels'][] = $d['label'];
			$data_ret['labels_d'][] = $d['label_d'];
			$data_ret['articlesCount'][] = ($d['articlesCount']) ? $d['articlesCount'] : 0;
			$data_ret['photosCount'][] = ($d['photosCount']) ? $d['photosCount'] : 0;
			$data_ret['cm'][] = $d['cm'];
			$data_ret['records'][] = $d['records'];

		}



		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $data_ret;
	}

}