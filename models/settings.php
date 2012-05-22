<?php

namespace models;
use \F3 as F3;
use \timer as timer;

class settings {
	function __construct() {


	}

	public static function getSettings($application = "ab", $ID = "") {
		$timer = new timer();
		$return = array();
		if ($application == "ab") {
			$columns = array(
				"client"                 => array(
					"c"=> "client",
					"h"=> "Client"
				),
				"publishDate"            => array(
					"c"=> "publishDate",
					"h"=> "Date"
				),
				"size"                   => array(
					"c"=> "size",
					"h"=> "Size"
				),

				"colour"                 => array(
					"c"=> "colour",
					"h"=> "Colour"
				),
				"colourSpot"             => array(
					"c"=> "colourSpot",
					"h"=> "Colour&nbsp;Spot"
				),
				"rate_C"                 => array(
					"c"=> "rate_C",
					"h"=> "Rate"
				),
				"totalCost_C"            => array(
					"c"=> "totalCost_C",
					"h"=> "Charged"
				),
				"totalShouldbe_C"        => array(
					"c"=> "totalShouldbe_C",
					"h"=> "Cost"
				),
				"discount"               => array(
					"c"=> "discount",
					"h"=> "Disc"
				),

				"agencyDiscount"         => array(
					"c"=> "agencyDiscount",
					"h"=> "A.Disc"
				),
				"placing"                => array(
					"c"=> "placing",
					"h"=> "Placing"
				),
				"category"               => array(
					"c"=> "category",
					"h"=> "Category"
				),
				"type"                   => array(
					"c"=> "type",
					"h"=> "Type"
				),
				"accNum"                 => array(
					"c"=> "accNum",
					"h"=> "Acc.Num"
				),
				"account"                => array(
					"c"=> "account",
					"h"=> "Account"
				),

				"orderNum"               => array(
					"c"=> "orderNum",
					"h"=> "Order&nbsp;Num"
				),
				"keyNum"                 => array(
					"c"=> "keyNum",
					"h"=> "Key&nbsp;Num"
				),
				"userName"               => array(
					"c"=> "userName",
					"h"=> "User"
				),
				"datein"                 => array(
					"c"=> "datein",
					"h"=> "Date"
				),
				"checked"                => array(
					"c"=> "checked",
					"h"=> "Checked"
				),
				"checked_date"           => array(
					"c"=> "checked_date",
					"h"=> "Checked&nbsp;Date"
				),
				"repeat"                 => array(
					"c"=> "repeat",
					"h"=> "Repeat"
				),


				"remark"                 => array(
					"c"=> "remark",
					"h"=> "Remark"
				),


				"marketer"               => array(
					"c"=> "marketer",
					"h"=> "Marketer"
				),
				"percent_diff"           => array(
					"c"=> "percent_diff",
					"h"=> "Disc %"
				)
			);
			$return["columns"] = $columns;
		}


		$timer->stop("Models - settings - getSettings", func_get_args());
		return $return;
	}

	public static function getDefaults($application = "ab", $ID = "") {
		$timer = new timer();
		$return = array();
		if ($application == "ab") {
			$return = array(
				"list"=>array(
					"col"        => array(
						"client",
						"size",
						"colour",
						"remark",
						"marketer"
					),
					"group"      => "placing",
					"order"      => "ASC",
					"count"      => "5",
					"provisional"=> array(
						"highlight"=> "checked",
						"filter"   => "*"
					)
				),
				"form"=>array(
					"type"=>"1"
				),
				"last_marketer"=>""
			);
		}


		$timer->stop("Models - settings - getDefaults", func_get_args());
		return $return;
	}


}