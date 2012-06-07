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

			);
			$return["columns"] = $columns;

		$groupBy = array();
		$groupBy[] = array(
			"n"=> "Placing",
			"g"=> "placing"
		);
		$groupBy[] = array(
			"n"=> "Type",
			"g"=> "type"
		);
		$groupBy[] = array(
			"n"=> "Colours",
			"g"=> "colours"
		);
		$groupBy[] = array(
			"n"=> "Marketers",
			"g"=> "marketer"
		);
		$groupBy[] = array(
			"n"=> "Columns",
			"g"=> "columns"
		);
		$groupBy[] = array(
			"n"=> "Discount %",
			"g"=> "discountPercent"
		);
		$groupBy[] = array(
			"n"=> "Account Status",
			"g"=> "accountStatus"
		);
		$groupBy[] = array(
			"n"=> "Layout Pages",
			"g"=> "pages"
		);
		$groupBy[] = array(
			"n"=> "No Ordering",
			"g"=> "none"
		);
		$groupBy[] = array(
			"n"=> "Production",
			"g"=> "material_production"
		);


		$return["groupby"] = $groupBy;


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


}