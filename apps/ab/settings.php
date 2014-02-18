<?php

namespace apps\ab;

use \timer as timer;

class settings {
	function __construct() {


	}

	public static function _available($permissions=array()) {
		$timer = new timer();
		$f3 = \Base::instance();
		$return = array();
		$columns = array(
			"client"                 => array(
				"h"=> "Client",
				"d"=>"Used as a heading / description for the record",
				"m"=>"120"
			),
			"publishDate"            => array(

				"h"=> "Publish&nbsp;Date",
				"m"=>60
			),
			"size"                   => array(
				"o"=> "`totalspace`",
				"h"=> "Size",
				"w"=>60
			),

			"colour"                 => array(
				"h"=> "Colour",
				"d"=>"(Adverts) Is the booking a full colour, spot colour, or a black and white booking. tarrif rates can be changed according to these values"
			),
			"colourLabel"             => array(
				"h"=> "Colour&nbsp;Label"
			),
			"rate_C"                 => array(
				"o"=> "`rate`",
				"h"=> "Rate"
			),
			"totalCost_C"            => array(
				"o"=> "`totalCost`",
				"h"=> "Net&nbsp;Cost"
			),
			"totalShouldbe_C"        => array(
				"o"=> "`totalShouldbe`",
				"h"=> "Gross&nbsp;Cost"
			),
			"discount"               => array(
				"h"=> "Disc"
			),

			"agencyDiscount"         => array(
				"h"=> "A.Disc"
			),
			"placing"                => array(
				"o"=> "`ab_bookings`.`placing`",
				"h"=> "Placing",
				"d"=>"(Adverts) Where in the publication (group) the booking should be placed"
			),
			"category"               => array(
				"h"=> "Category"
			),
			"type"                   => array(
				"h"=> "Type"
			),
			"accNum"                 => array(
				"h"=> "Acc.Num",
				"o"=>"`ab_accounts`.`accNum`"
			),
			"account"                => array(
				"h"=> "Account"
			),
			"account_email"                => array(
				"h"=> "Account&nbsp;Email"
			),
			"account_phone"                => array(
				"h"=> "Account&nbsp;Phone"
			),

			"invoiceNum"               => array(
				"h"=> "Invoice&nbsp;Num"
			),
			"orderNum"               => array(
				"h"=> "Order&nbsp;Num"
			),
			"keyNum"                 => array(
				"h"=> "Key&nbsp;Num"
			),
			"payment_method" => array(
				"h" => "Payment&nbsp;Method"
			),
			"payment_method_note" => array(
				"h" => "Payment&nbsp;Method&nbsp;Note"
			),
			"insertsLabel"=>array(
				"o"=> "`ab_inserts_types`.`insertsLabel`",
				"h"=> "Inserts&nbsp;Type"
			),
			"userName"               => array(
				"h"=> "User"
			),
			"datein"                 => array(
				"h"=> "Date\\Time",
				"d"=>"Date and Time the booking was added",
				"m" => 60
			),
			"datein_date"                 => array(
				"h"=> "Date",
				"d" => "Date the booking was added",
				"w" => 60
			),
			"dateChanged"                 => array(
				"h"=> "Date&nbsp;Changed",
				"d" => "Date the booking was last changed",
				"m" => 60
			),
			"checked"                => array(
				"h"=> "Checked",
				"b"=> true
			),
			"checked_date"           => array(
				"h"=> "Checked&nbsp;Date"
			),
			"checked_user"           => array(
				"h"=> "Checked&nbsp;User"
			),
			"repeat_from"                 => array(
				"h"=> "Repeat",
				"b"=> true
			),


			"remark"                 => array(
				"h"=> "Remark"
			),


			"marketer"               => array(
				"o"=> "`ab_marketers`.`marketer`",
				"h"=> "Marketer",
				"m"=>"120" // minimum width
			),
			"percent_diff"           => array(
				"o"=> "(totalShouldbe - totalCost)/totalShouldbe",
				"h"=> "Disc %",
				"w"=>40
			),
			"material_file_filename"               => array(
				"h"=> "File&nbsp;Name"
			),
			"material_file_filesize"               => array(
				"h"=> "File&nbsp;Size",
				"m"=> "60"
			),
			"material_source"               => array(
				"h"=> "Material&nbsp;Source"
			),
			"material_production"               => array(
				"h"=> "Material&nbsp;Production",
				"o"=> "`ab_production`.`production`",
			),
			"material_status"               => array(
				"h"=> "Material",
				"b"=>true
			),
			"material_date"               => array(
				"h"=> "Material&nbsp;Date"
			),
			"material_approved"               => array(
				"h"=> "Material&nbsp;Approved",
				"b"=>true
			),
			"page"               => array(
				"h"=> "Page",
			),

			"deleted"               => array(
				"h"=> "Deleted",
				"b"=> true
			),
			"deleted_user"               => array(
				"h"=> "Deleted&nbsp;By",
			),
			"deleted_date"               => array(
				"h"=> "Deleted&nbsp;Date",
			),
			"deleted_reason"               => array(
				"h"=> "Deleted&nbsp;Reason",
			),



		);
		$c = array();
		foreach ($columns as $column=>$values){
			$values['c'] = $column;

			if (!isset($values['o']))$values['o'] = "`".$column."`";

			$c[$column] = $values;
		}


		$return["columns"] = $c;

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
			"invoiced"=> array(
				"n"=> "Invoiced Status",
				"g"=> "invoicedStatus"
			),
			"payment_method"=> array(
				"n"=> "Payment Method",
				"g"=> "payment_method"
			),
			"none"=> array(
				"n"=> "No Grouping",
				"g"=> "none"
			)
		);




		$sections = array(
			"all"=>array("placing","type","colours","marketer","columns","discountPercent","accountStatus","pages","material_production","invoiced","payment_method","none"),
			"provisional"=>array("placing","type","colours","marketer","columns","discountPercent","accountStatus","invoiced","payment_method","none"),
			"production"=>array("placing","type","colours","columns","pages","material_production","none"),
			"search"=>array("date","placing","type","colours","marketer","columns","discountPercent","accountStatus","pages","material_production","invoiced",	"payment_method","none"),
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


		if (isset($permissions['lists']['fields'])) {
			foreach ($permissions['lists']['fields'] as $key=> $value) {
				if ($value == 0) {
					if (isset($return['columns'][$key])) unset($return['columns'][$key]);
					if (isset($return['columns'][$key . "_C"])) unset($return['columns'][$key . "_C"]);
				}
			}
		}

		$cfg = $f3->get("CFG");

		if (!$cfg['upload']['material']){
			if (isset($return['columns']['material_file_filename'])) unset($return['columns']['material_file_filename']);
			if (isset($return['columns']['material_file_filesize'])) unset($return['columns']['material_file_filesize']);
		}



		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function defaults($application = "ab", $ID = "") {
		$timer = new timer();
		$f3 = \Base::instance();
		$return = array();
			$settings = array(
				"provisional"=>array(
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
					"search" => ""

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
					"search" => ""


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
						"g"=> "date",
						"o"=> "DESC"
					),
					"order"      => array(
						"c"=> "client",
						"o"=> "ASC"
					),
					"count"      => "5",
					"search"=> array(
						"search"=> "",
						"dates" => ""
					)

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
					"search"=>array(
						"search"=>"",
						"dates"=>""
					)


				),
				"overview"=>array(
					"highlight"=>"material"
				),

				"form"=>array(
					"type"=>"1",
					"last_marketer"=>"",
					"last_category"=>""
				),
				"layout"=>array(
					"placingID"=>array()
				),

				"admin_accounts"=>array(
					"order"=>array(
						"c"=>"account",
						"o"=>"ASC"
					)
				),
				"admin_users"=>array(
					"order"=>array(
						"c"=>"fullName",
						"o"=>"ASC"
					)
				),
				"admin_marketers"=>array(
					"order"=>array(
						"c"=>"marketer",
						"o"=>"ASC"
					)
				),
				"admin_marketers_targets"=>array(
					"marketerID"=>"",
					"order"=>array(
						"c" => "date_to",
						"o" => "DESC"
					)
				),
				"admin_production"=>array(
					"order"=>array(
						"c"=>"production",
						"o"=>"ASC"
					)
				),
				"admin_dates"=>array(
					"order"=>array(
						"c"=>"publish_date",
						"o"=>"DESC"
					)
				),
				"reports_publication"=>array(
					"years"=>"",
					"timeframe"=>"",
					"combined"=>"0",
					"dir"=>"d", // under = d, over = u charged
					"col" => array(
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
					"tolerance"=>25
				),
				"reports_publication_section"=>array(
					"years"=>"",
					"timeframe"=>"",
					"combined"=>"0",
					"dir"=>"d", // under = d, over = u charged
					"col" => array(
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
					"tolerance"=>25
				),
				"reports_publication_placing"=>array(
					"years"=>"",
					"timeframe"=>"",
					"combined"=>"0",
					"dir"=>"d", // under = d, over = u charged
					"col" => array(
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
					"tolerance"=>25
				),
				"reports_account"=>array(
					"years"=>"",
					"timeframe"=>"",
					"combined"=>"0",
					"dir"=>"d", // under = d, over = u charged
					"ID"=>array(),
					"col" => array(
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
					"tolerance"=>25
				),
				"reports_marketer"=>array(
					"years"=>"",
					"timeframe"=>"",
					"combined"=>"0",
					"dir"=>"d", // under = d, over = u charged
					"ID"=>array(),
					"col" => array(
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
					"tolerance"=>25
				),
				"reports_marketer_targets"=>array(
					"ID"=>"",

					"filter"=>'1'
				),

				"reports_production"=>array(
					"years"=>"",
					"timeframe"=>"",
					"combined"=>"0",
					"dir"=>"d", // under = d, over = u charged
					"ID"=>array(),
					"col" => array(
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
					"tolerance"=>25
				),
				"reports_category"=>array(
					"years"=>"",
					"timeframe"=>"",
					"combined"=>"0",
					"dir"=>"d", // under = d, over = u charged
					"ID"=>array(),
					"col" => array(
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
					"tolerance"=>25
				)

			);

		$return =$settings;



		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}



}