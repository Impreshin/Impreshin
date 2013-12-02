<?php

namespace apps\nf;

use \timer as timer;

class settings {
	function __construct() {


	}

	public static function _available($permissions=array()) {
		$timer = new timer();
		$f3 = \Base::instance();
		$return = array();

		/*
		 * name => array(
		 *  h =>"heading",
		 *  o =>"order by",
		 *  d =>"description",
		 *  b =>"show a tick mark if its 1",
		 *  m => "minimum width",
		 *  w => "width",
		 * )
		 */

		$columns = array(
			"title"                 => array(
				"h"=> "Title",
			),
			"datein"=>array(
				"h"=>"Date&nbsp;Created",
				"m"=>60
			),
			"dateChanged"=>array(
				"h"=>"Date&nbsp;Changed",
				"m"=>60
			),
			
			"stage"=>array(
				"h"=>"Stage"
			),
			"author"=>array(
				"h"=>"Author"
			),
			"cm"=>array(
				"h"=>"Cm",
				"w"=>40
			),
			"words"=>array(
				"h"=>"Words",
				"w"=>40
			),
			"priority"=>array(
				"h"=>"Priority"
			),
			"percent_last"=>array(
				"h"=>"%&nbsp;last",
				"d"=>"The percentage the body was changed since the edit before the current"
			),
			"percent_orig"=>array(
				"h"=>"%&nbsp;Chan.",
				"d"=>"The percentage the body was changed since the article was sent from 'Draft'"
			),
			
			"newsbooks"=>array(
				"h"=>"Newsbooks",
				"o"=>"global_publications.publication, global_dates.publish_date"
			),
			"page"=>array(
				"h"=>"Page",
				"o"=>"global_pages.page",
				
			),
			
			"category"=>array(
				"h"=>"Category",
				"o"=>"nf_categories.category"
			),
			"commentCount"=>array(
				"h"=>"Comments",
				"o"=>"commentCount",
				"w"=>40
			),
			"photosCount"=>array(
				"h"=>"Photos",
				"o"=>"photosCount",
				"w"=>40
			),
			"filesCount"=>array(
				"h"=>"Files",
				"o"=>"filesCount",
				"w"=>40
			),
			"locked_fullName"=>array(
				"h"=>"Locked&nbsp;By",
				"o"=>"locked_fullName"
			),
			"locked"=>array(
				"b"=>"locked",
				"h"=>"Locked",
				"o"=>"locked",
				"w"=>30
			),
			"placed"=>array(
				"b"=>"placed",
				"h"=>"Placed",
				"o"=>"placed",
				"w"=>30
			),

			"deleted"               => array(
				"h"=> "Deleted",
				"b"=> true
			),
			"deleted_fullName"               => array(
				"h"=> "Deleted&nbsp;By",
			),
			"deleted_date"               => array(
				"h"=> "Deleted&nbsp;Date",
			),
			"deleted_reason"               => array(
				"h"=> "Deleted&nbsp;Reason",
			),
			"archived"               => array(
				"h"=> "Archived",
				"b"=> true,
				"w"=>30
				
			),
			"archived_date"               => array(
				"h"=> "Archived&nbsp;Date",
				"m"=>60
			),

		);
		$c = array();
		foreach ($columns as $column=>$values){
			$values['c'] = $column;

			if (!isset($values['o']))$values['o'] = "`".$column."`";

			$c[$column] = $values;
		}


		$return["columns"] = $c;

		/*
		 * name => array(
		 *  n=>"Heading",
		 *  g=>"group by"
		 * )
		 */
		$groupByoptions = array(

			"author"=> array(
				"n"=> "Authors",
				"g"=> "author"
			),
			"category"=> array(
				"n"=> "Category",
				"g"=> "category"
			),
			"stage"=> array(
				"n"=> "Stage",
				"g"=> "stage"
			),
			"priority"=> array(
				"n"=> "Priority",
				"g"=> "priority"
			),
			"page"=> array(
				"n"=> "Page",
				"g"=> "page"
			),
			"type"=> array(
				"n"=> "Type",
				"g"=> "type"
			),
			"none"=> array(
				"n"=> "No Grouping",
				"g"=> "none"
			),
		);




		$sections = array(
			"provisional"=>array("author","category","stage","priority","type","none"),
			"newsbook"=>array("author","category","stage","priority","page","type","none"),
			"production"=>array("author","category","priority","page","type","none"),
			"search"=>array("author","category","stage","priority","type","none"),
			"records_newsbook"=>array("author","category","stage","priority","type","none"),
			"reports_author_newsbook"=>array("category","stage","priority","type","none"),
			"reports_category_figures"=>array("category","stage","priority","type","none"),
			"reports_author_submitted"=>array("category","stage","priority","type","none"),
			"reports_publication_figures"=>array("category","stage","priority","type","none"),
		
		);

		$groupby = array();
		foreach ($sections as $key=>$value){
			$opts = array();
			foreach($value as $col){
				$opts[] = $groupByoptions[$col];
			}
			$groupby[$key] = $opts;
		}



		$return["groupby"] = $groupby;


		$return["allowedFileTypes"] = array( // filetype => icon/type => extentions
			"1"=>array(
				"img"=> array("jpg", "gif", "png", "jpeg"),
			),
			"2"=>array(
				"doc" => array("doc", "docx"),
				"xls" => array("xls", "xlsx"),
				"pdf" => array("pdf"),
				"file" => array("txt")
			),
			"3"=>array(
				"vid"=>array("avi")
			)

		);

		

		if (isset($permissions['lists']['fields'])) {
			foreach ($permissions['lists']['fields'] as $key=> $value) {
				if ($value == 0) {
					if (isset($return['columns'][$key])) unset($return['columns'][$key]);
					if (isset($return['columns'][$key . "_C"])) unset($return['columns'][$key . "_C"]);
				}
			}
		}

		$cfg = $f3->get("CFG");




		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function defaults($application = "ab", $ID = "") {
		$timer = new timer();
		$f3 = \Base::instance();
		$return = array();
		
		$defColumns = array(
			"title",
			"author",
			"stage",
			"cm",
			"words",
			"priority"
		);
		$defColumns_reports_author = array(
			"title",
			"stage",
			"cm",
			"words",
			"priority",
			"photosCount"
		);
		
			$settings = array(
				"provisional"=>array(
					"col"        => $defColumns,
					"group"      => array(
						"g"=>"stage",
						"o"=>"ASC"
					),
					"order"      => array(
						"c"=> "title",
						"o"=> "ASC"
					),
					"count"      => count($defColumns),
					"highlight"=> "locked",
					"stage"=> "*",
					"filter"   => "*",
					"search" => "",

				),
				"newsbook"=>array(
					"col"        => $defColumns,
					"group"      => array(
						"g"=>"stage",
						"o"=>"ASC"
					),
					"order"      => array(
						"c"=> "title",
						"o"=> "ASC"
					),
					"count"      => count($defColumns),
					"highlight"=> "ready",
					"filter"   => "*",
					"search" => "",
					"stageID"=>"0"

				),
				"layout"=>array(
					"categoryID"=>array(),
					"filter"   => "*",
					"order"      => array(
						"c"=> "title",
						"o"=> "ASC"
					),
				),
				"production"=>array(
					"col"        => $defColumns,
					"group"      => array(
						"g"=>"stage",
						"o"=>"ASC"
					),
					"order"      => array(
						"c"=> "title",
						"o"=> "ASC"
					),
					"count"      => count($defColumns),
					"type_switch"=> "1",
					"filter"   => "*",
					"highlight"   => "ready",
					"search" => "",

				),
				"form"=>array(
					"last_type"=>"1",

				),
				"search"=> array(
					"col"        => $defColumns,
					"group"      => array(
						"g"=> "none",
						"o"=> "ASC"
					),
					"order"      => array(
						"c"=> "title",
						"o"=> "ASC"
					),
					"count"      => count($defColumns),
					"search"=> array(
						"search"=> "",
						"dates" => date("Y-m-d",strtotime(date("Y")."-".date('m')."-".'1')) . " to " . date("Y-m-d",strtotime(date("Y")."-".date('m')."-".date('t')))
					)

				),
				"records_newsbook"=> array(
					"col"        => $defColumns,
					"group"      => array(
						"g"=> "author",
						"o"=> "ASC"
					),
					"order"      => array(
						"c"=> "title",
						"o"=> "ASC"
					),
					"count"      => count($defColumns),
					"dID"=> array()

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
						"g"=> "none",
						"o"=> "ASC"
					),
					"order"      => array(
						"c"=> "datein",
						"o"=> "DESC"
					),
					"count"      => "6",
					"search"=>array(
						"search"=>"",
						"dates" => date("Y-m-d",strtotime(date("Y")."-".date('m')."-".'1')) . " to " . date("Y-m-d",strtotime(date("Y")."-".date('m')."-".date('t')))
					)


				),
				"reports_author_submitted"=>array(
					"years"=>"",
					"timeframe"=>"",
					"combined"=>"0",
					"ID"=>array(),
					"col" => $defColumns_reports_author,
					"group"      => array(
						"g"=>"type",
						"o"=>"ASC"
					),
					"order"      => array(
						"c"=> "title",
						"o"=> "ASC"
					),
					"count"      => count($defColumns_reports_author),
					"tolerance"=>25
				),
				"reports_author_newsbook"=>array(
					"years"=>"",
					"timeframe"=>"",
					"filter"=>"*",
					"ID"=>array(),
					"col" => $defColumns_reports_author,
					"group"      => array(
						"g"=>"type",
						"o"=>"ASC"
					),
					"order"      => array(
						"c"=> "title",
						"o"=> "ASC"
					),
					"count"      => count($defColumns_reports_author),
					"tolerance"=>25
				),
				"reports_publication_figures"=>array(
					"years"=>"",
					"timeframe"=>"",
					"filter"=>"*",
					"ID"=>array(),
					"col" => $defColumns_reports_author,
					"group"      => array(
						"g"=>"type",
						"o"=>"ASC"
					),
					"order"      => array(
						"c"=> "title",
						"o"=> "ASC"
					),
					"count"      => count($defColumns_reports_author),
					"tolerance"=>25
				),
				"reports_category_figures"=>array(
					"years"=>"",
					"timeframe"=>"",
					"filter"=>"*",
					"ID"=>array(),
					"col" => $defColumns_reports_author,
					"group"      => array(
						"g"=>"type",
						"o"=>"ASC"
					),
					"order"      => array(
						"c"=> "title",
						"o"=> "ASC"
					),
					"count"      => count($defColumns_reports_author),
					"tolerance"=>25
				),
				
				"admin_users"=>array(
					"order"=>array(
						"c"=>"fullName",
						"o"=>"ASC"
					)
				),

				"admin_dates"=>array(
					"order"=>array(
						"c"=>"publish_date",
						"o"=>"DESC"
					)
				),


			);

		$return =$settings;



		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}



}