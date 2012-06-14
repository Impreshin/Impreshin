<?php

namespace models\ab;
use \F3 as F3;
use \timer as timer;

class settings {
	function __construct() {


	}

	public static function getSettings($ID = "") {
		$timer = new timer();
		$return = array();
			$columns = array(
				"client"                 => array(
					"c"=> "client",
					"o"=> "client",
					"h"=> "Client"
				),
				"publishDate"            => array(
					"c"=> "publishDate",
					"o"=> "publishDate",
					"h"=> "Date"
				),
				"size"                   => array(
					"c"=> "size",
					"o"=> "totalspace",
					"h"=> "Size"
				),

				"colour"                 => array(
					"c"=> "colour",
					"o"=> "colour",
					"h"=> "Colour"
				),
				"colourSpot"             => array(
					"c"=> "colourSpot",
					"o"=> "colourSpot",
					"h"=> "Colour&nbsp;Spot"
				),
				"rate_C"                 => array(
					"c"=> "rate_C",
					"o"=> "rate",
					"h"=> "Rate"
				),
				"totalCost_C"            => array(
					"c"=> "totalCost_C",
					"o"=> "totalCost",
					"h"=> "Charged"
				),
				"totalShouldbe_C"        => array(
					"c"=> "totalShouldbe_C",
					"o"=> "totalShouldbe",
					"h"=> "Cost"
				),
				"discount"               => array(
					"c"=> "discount",
					"o"=> "discount",
					"h"=> "Disc"
				),

				"agencyDiscount"         => array(
					"c"=> "agencyDiscount",
					"o"=> "agencyDiscount",
					"h"=> "A.Disc"
				),
				"placing"                => array(
					"c"=> "placing",
					"o"=> "ab_bookings.placing",
					"h"=> "Placing"
				),
				"category"               => array(
					"c"=> "category",
					"o"=> "category",
					"h"=> "Category"
				),
				"type"                   => array(
					"c"=> "type",
					"o"=> "type",
					"h"=> "Type"
				),
				"accNum"                 => array(
					"c"=> "accNum",
					"o"=> "accNum",
					"h"=> "Acc.Num"
				),
				"account"                => array(
					"c"=> "account",
					"o"=> "account",
					"h"=> "Account"
				),

				"orderNum"               => array(
					"c"=> "orderNum",
					"o"=> "orderNum",
					"h"=> "Order&nbsp;Num"
				),
				"keyNum"                 => array(
					"c"=> "keyNum",
					"o"=> "keyNum",
					"h"=> "Key&nbsp;Num"
				),
				"userName"               => array(
					"c"=> "userName",
					"o"=> "userName",
					"h"=> "User"
				),
				"datein"                 => array(
					"c"=> "datein",
					"o"=> "datein",
					"h"=> "Date"
				),
				"checked"                => array(
					"c"=> "checked",
					"o"=> "checked",
					"h"=> "Checked",
					"b"=> true
				),
				"checked_date"           => array(
					"c"=> "checked_date",
					"o"=> "checked_date",
					"h"=> "Checked&nbsp;Date"
				),
				"checked_user"           => array(
					"c"=> "checked_user",
					"o"=> "checked_user",
					"h"=> "Checked&nbsp;User"
				),
				"repeat"                 => array(
					"c"=> "repeat",
					"o"=> "repeat",
					"h"=> "Repeat",
					"b"=> true
				),


				"remark"                 => array(
					"c"=> "remark",
					"o"=> "remark",
					"h"=> "Remark"
				),


				"marketer"               => array(
					"c"=> "marketer",
					"o"=> "ab_bookings.marketer",
					"h"=> "Marketer"
				),
				"percent_diff"           => array(
					"c"=> "percent_diff",
					//"o"=> "percent_diff",
					"h"=> "Disc %"
				),
				"material_source"               => array(
					"c"=> "material_source",
					"o"=> "material_source",
					"h"=> "Material&nbsp;Source"
				),
				"material_production"               => array(
					"c"=> "material_production",
					"o"=> "material_production",
					"h"=> "Material&nbsp;Production"
				),
				"material_status"               => array(
					"c"=> "material_status",
					"o"=> "material_status",
					"h"=> "Material",
					"b"=>true
				),
				"material_date"               => array(
					"c"=> "material_date",
					"o"=> "material_date",
					"h"=> "Material&nbsp;Date"
				),
				"material_approved"               => array(
					"c"=> "material_approved",
					"o"=> "material_approved",
					"h"=> "Material&nbsp;Approved",
					"b"=>true
				),
				"page"               => array(
					"c"=> "page",
					"o"=> "page",
					"h"=> "Page",
				),

				"deleted"               => array(
					"c"=> "deleted",
					"o"=> "deleted",
					"h"=> "Deleted",
					"b"=> true
				),
				"deleted_user"               => array(
					"c"=> "deleted_user",
					"o"=> "deleted_user",
					"h"=> "Deleted&nbsp;By",
				),
				"deleted_date"               => array(
					"c"=> "deleted_date",
					"o"=> "deleted_date",
					"h"=> "Deleted&nbsp;Date",
				),
				"deleted_reason"               => array(
					"c"=> "deleted_reason",
					"o"=> "deleted_reason",
					"h"=> "Deleted&nbsp;Reason",
				),


			);
			$return["columns"] = $columns;

		$groupByoptions = array(
			"placing"=>array(
				"n"=> "Placing",
				"g"=> "placing"
			),
			"date"=>array(
				"n"=> "Date",
				"g"=> "date"
			),
			"type"=> array(
				"n"=> "Type",
				"g"=> "type"
			),
			"colours"=> array(
				"n"=> "Colours",
				"g"=> "colours"
			),
			"marketer"=> array(
				"n"=> "Marketers",
				"g"=> "marketer"
			),
			"columns"=> array(
				"n"=> "Columns",
				"g"=> "columns"
			),
			"discountPercent"=> array(
				"n"=> "Discount %",
				"g"=> "discountPercent"
			),
			"accountStatus"=> array(
				"n"=> "Account Status",
				"g"=> "accountStatus"
			),
			"pages"=>array(
				"n"=> "Layout Pages",
				"g"=> "pages"
			),
			"material_production"=> array(
				"n"=> "Production",
				"g"=> "material_production"
			),
			"none"=> array(
				"n"=> "No Ordering",
				"g"=> "none"
			)
		);




		$sections = array(
			"all"=>array("placing","type","colours","marketer","columns","discountPercent","accountStatus","pages","material_production","none"),
			"provisional"=>array("placing","type","colours","marketer","columns","discountPercent","accountStatus","none"),
			"production"=>array("placing","type","colours","columns","pages","material_production","none"),
			"search"=>array("date","placing","type","colours","marketer","columns","discountPercent","accountStatus","pages","material_production","none"),
		);

		$groupby = array();
		foreach ($sections as $key=>$value){
			$opts = array();
			foreach($value as $col){
				$opts[] = $groupByoptions[$col];
			}
			$groupby[$key] = $opts;
		}

		$groupby['deleted'] = $groupby['search'];


		$return["groupby"] = $groupby;
		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getDefaults($application = "ab", $ID = "") {
		$timer = new timer();
		$return = array();
			$return = array(
				"list"=>array(
					"col"        => array(
						"client",
						"size",
						"colour",
						"remark",
						"marketer"
					),
					"group"      => array(
						"g"=>"placing",
						"o"=>"ASC"
					),
					"order"      => array(
						"c"=> "client",
						"o"=> "ASC"
					),
					"count"      => "5",
					"highlight"=> "checked",
					"filter"   => "*",

				),
				"production"=>array(
					"col"        => array(
						"client",
						"size",
						"colour",
						"remark",
						"marketer",
						"material_status",
					),
					"group"      => array(
						"g"=>"material_production",
						"o"=>"ASC"
					),
					"order"      => array(
						"c"=> "client",
						"o"=> "ASC"
					),
					"count"      => 6,
					"highlight"=> "material",
					"filter"   => "*",


				),
				"search"=> array(
					"col"        => array(
						"client",
						"size",
						"colour",
						"remark",
						"marketer"
					),
					"group"      => array(
						"g"=> "placing",
						"o"=> "ASC"
					),
					"order"      => array(
						"c"=> "client",
						"o"=> "ASC"
					),
					"count"      => "5",


				),
				"deleted"=> array(
					"col"        => array(
						"client",
						"size",
						"colour",
						"deleted_user",
						"deleted_date",
						"deleted_reason"
					),
					"group"      => array(
						"g"=> "date",
						"o"=> "ASC"
					),
					"order"      => array(
						"c"=> "client",
						"o"=> "ASC"
					),
					"count"      => "6",


				),

				"form"=>array(
					"type"=>"1"
				),
				"layout"=>array(
					"placingID"=>array()
				),
				"last_marketer"=>""
			);

		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}

	function _read($section){
		$timer = new timer();
		$settings = self::getSettings();
		$user = F3::get("user");

		$a = array();
		$b = array();
		if (isset($user['settings'][$section]['col']) && count($settings["columns"])) {

			foreach ($settings["columns"] as $col) {
				if (!in_array($col['c'], $user["settings"][$section]['col'])) {
					$col["s"] = "0";
					$a[] = $col;
				}

			}


			foreach ($user["settings"][$section]['col'] AS $col) {
				$v = $settings["columns"][$col];
				$v['s'] = '1';
				$b[] = $v;
			}

			$settings[1] = array_merge($b, $a);


		}



		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $settings;
	}
	function write(){

	}

}