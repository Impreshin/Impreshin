<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace controllers\ab\data;
use \F3 as F3;
use \timer as timer;
use \models\ab as models;
use \models\user as user;


class reports_publication_figures extends data {
	function __construct() {

		$user = F3::get("user");
		$userID = $user['ID'];
		if (!$userID) exit(json_encode(array("error" => F3::get("system")->error("U01"))));

	}

	function _data() {
		$timer = new timer();
		$user = F3::get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];

		$cID = $user['publication']['cID'];
		$section = "reports_publication_figures";
		$return = array();

		$settings = models\settings::_read($section);



		$publications = isset($_REQUEST['pubs']) ? $_REQUEST['pubs'] : "";

		$years_d = F3::get("DB")->exec("SELECT distinct year(publish_date) AS record_year FROM global_dates WHERE pID in ($publications) ORDER BY year(publish_date) DESC");


		$years = isset($_REQUEST['years'])?$_REQUEST['years']:""; //($settings['years'])? $settings['years']:$years_d[0]['record_year'];
		if (!$years){
			$years = $settings['years'];
			if (!$years){
				$years = array();
				$i = 0;
				foreach ($years_d as $d){
					if ($i++ < 3) $years[]= $d['record_year'];
				}
				$years = implode(",", $years);
			}

		}





		$return['comp'] = array();
		$return['charts'] = array();





		$values = array();
		$values[$section] = array(
			"years"=> $years,
			"timeframe"=>""
		);
		$values[$section]["pub_$pID"] = array(
			"pubs"=>	$publications
		);



		models\user_settings::save_setting($values);





		$y = array();
		$years = explode(",", $years);
		foreach ($years_d as $year){
			$y[] = array(
				"y"=>$year['record_year'],
				"s"=>(in_array($year['record_year'], $years))?"1":"0"
			);
		}

		$years = ($y);;

		$return['comp']['years']=$years;


		$months = array(
			array(
				"h"=>"January",
				"k"=>"01"
			),
			array(
				"h"=> "February",
				"k"=> "02"
			),
			array(
				"h"=> "March",
				"k"=> "03"
			),
			array(
				"h"=> "April",
				"k"=> "04"
			),
			array(
				"h"=> "May",
				"k"=> "05"
			),
			array(
				"h"=> "June",
				"k"=> "06"
			),
			array(
				"h"=> "July",
				"k"=> "07"
			),array(
				"h"=> "August",
				"k"=> "08"
			),array(
				"h"=> "September",
				"k"=> "09"
			),array(
				"h"=> "October",
				"k"=> "10"
			),
			array(
				"h"=> "November",
				"k"=> "11"
			),
			array(
				"h"=> "December",
				"k"=> "12"
			)
		);

		$y = array();
		foreach ($years as $year) {
			if ($year['s']){
				$y[] = $year['y'];
			}
		}
		$y = implode(",",$y);



		$where = "ab_bookings.pID in ($publications) AND year(publishDate) in ($y) AND checked = '1'";
		$select = "publishDate, totalCost, totalspace, ab_bookings.pID as pID";

		$d = models\bookings::getAll_select($select,$where, "global_dates.publish_date ASC");


		$blank = array(
			"totals" => 0,
			"cm"     => 0,
			"records"=> 0,
		);
		$margin = 10; // %


		$data = array();
		foreach ($d as $record){
			$year = date("Y", strtotime($record['publishDate']));
			$month = date("m", strtotime($record['publishDate']));
			if (!isset($data[$year])){
				$data[$year]= array();
			}
			if (!isset($data[$year][$month])){
				$data[$year][$month]= $blank;
			}

			$data[$year][$month]['totals'] = $data[$year][$month]['totals'] + $record['totalCost'];
			$data[$year][$month]['cm'] = $data[$year][$month]['cm'] + $record['totalspace'];
			$data[$year][$month]['records'] = $data[$year][$month]['records'] + 1;

		}


		$ret = array();
		foreach ($months as $month){
			$r = array(
				"month"=>$month['h'],
				"data"=>array(),
				"averages"=> $blank
			);
			$i_t = 0;
			$i_c = 0;
			$i_r = 0;
			$totals = $blank;
			foreach ($years as $year){
				if ($year['s']){

					$total = isset($data[$year['y']][$month['k']]['totals']) ? ($data[$year['y']][$month['k']]['totals']) : 0;
					$cm= isset($data[$year['y']][$month['k']]['cm']) ? ($data[$year['y']][$month['k']]['cm']) : 0;
					$records= isset($data[$year['y']][$month['k']]['records']) ? ($data[$year['y']][$month['k']]['records']) : 0;


					$totals['totals'] = $totals['totals'] + $total;
					$totals['cm'] = $totals['cm'] + $cm;
					$totals['records'] = $totals['records'] + $records;

					if (isset($data[$year['y']][$month['k']]['totals'])){
						$i_t++;
					}
					if (isset($data[$year['y']][$month['k']]['cm'])){
						$i_c++;
					}
					if (isset($data[$year['y']][$month['k']]['records'])){
						$i_r++;
					}


					$r['data'][] = array(
						"year"=>$year['y'],
						"totals"=>($total)? ($total):"",
						"cm"=>($cm)?$cm:"",
						"records"=>($records)?$records:"",
						"d"=>array(
							"totals"=>"",
							"cm"=>"",
							"records"=>""
						)
					);


				}

			}

			$r['averages']['totals'] = ($i_t)?$totals['totals'] / $i_t: $totals['totals'];
			$r['averages']['cm'] = ($i_c) ? $totals['cm'] / $i_c : $totals['cm'];
			$r['averages']['records'] = ($i_r) ? $totals['records'] / $i_r : $totals['records'];



			$ndata = array();
			foreach ($r['data'] as $rec) {
				$col = "totals";
				$figs_c_totals = array(
					 $r['averages'][$col] + ($r['averages'][$col] * ($margin / 100)),
					 $r['averages'][$col] - ($r['averages'][$col] * ($margin / 100)),
				);
				if ($rec[$col]> $figs_c_totals[0]){
					$rec['d'][$col] = "u";
				} else if ($rec[$col] < $figs_c_totals[1] ){
					$rec['d'][$col] = "d";
				}
				$col = "cm";
				$figs_c_totals = array(
					 $r['averages'][$col] + ($r['averages'][$col] * ($margin / 100)),
					 $r['averages'][$col] - ($r['averages'][$col] * ($margin / 100)),
				);
				if ($rec[$col]> $figs_c_totals[0]){
					$rec['d'][$col] = "u";
				} else if ($rec[$col] < $figs_c_totals[1] ){
					$rec['d'][$col] = "d";
				}
				$col = "records";
				$figs_c_totals = array(
					 $r['averages'][$col] + ($r['averages'][$col] * ($margin / 100)),
					 $r['averages'][$col] - ($r['averages'][$col] * ($margin / 100)),
				);
				if ($rec[$col]> $figs_c_totals[0]){
					$rec['d'][$col] = "u";
				} else if ($rec[$col] < $figs_c_totals[1] ){
					$rec['d'][$col] = "d";
				}





				$rec['totals']= ($rec['totals']) ? currency($rec['totals']) : "";
				$ndata[] = $rec;
			}
			$r['data'] = $ndata;

			$ret[] = $r;
		}





		$return['comp']['data'] = $ret;
		$return['pubs'] = $publications;


		$timer->stop("Report - ". __CLASS__ . "->" .  __FUNCTION__ );
		return $GLOBALS["output"]['data'] = $return;
	}


}
