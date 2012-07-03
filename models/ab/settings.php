<?php

namespace models\ab;
use \F3 as F3;
use \timer as timer;

class settings {
	function __construct() {


	}

	public static function getSettings($permissions=array()) {
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
					"h"=> "Marketer",
					"m"=>"120" // minimum width
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


		if (isset($permissions['lists']['fields'])) {
			foreach ($permissions['lists']['fields'] as $key=> $value) {
				if ($value == 0) {
					if (isset($return['columns'][$key])) unset($return['columns'][$key]);
					if (isset($return['columns'][$key . "_C"])) unset($return['columns'][$key . "_C"]);
				}
			}
		}



		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getDefaults($application = "ab", $ID = "") {
		$timer = new timer();
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
					"last_marketer"=>""
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
					"order"=>array(
						"c"=>"marketer",
						"o"=>"ASC"
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
				)

			);

		$return['settings'] = $settings;



		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function _read($section, $permission=array()){
		$user = F3::get("user");
		$timer = new timer();
		$settings = self::getSettings($user['permissions']);
		$defaults = self::getDefaults();
		$settings_raw = $settings;

		$user_settings = new user_settings();
		$user_settings = $user_settings->_read($user['ID']);
		$user_settings['settings'] = @unserialize($user_settings['settings']);








		if ($user_settings['settings']){
			$user_settings = array_replace_recursive((array)$defaults, (array)($user_settings) ? $user_settings : array());
		} else {
			$user_settings = $defaults;
		}





		$return = array();

		//test_array($user_settings);
		$return = $user_settings['settings'][$section];





		if (isset($user_settings['settings'][$section]['col']) && count($settings["columns"])) {
			$columns = array();

			foreach ($user_settings['settings'][$section]['col'] as $col){
				if (isset($settings['columns'][$col])){
					$columns[] = $settings['columns'][$col];
				}

			}



			$return['col'] = $columns;
			$return['count']=count($columns);
		}
		if (isset($settings_raw['groupby'][$section])) $return['groupby']= $settings_raw['groupby'][$section];




		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}
	function write(){

	}

}