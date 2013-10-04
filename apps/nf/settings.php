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
				"h"=>"Date&nbsp;Created"
			),
			"dateChanged"=>array(
				"h"=>"Date&nbsp;Changed"
			),
			
			"stage"=>array(
				"h"=>"Stage"
			),
			"author"=>array(
				"h"=>"Author"
			),
			"cm"=>array(
				"h"=>"Cm"
			),
			"words"=>array(
				"h"=>"Words"
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
				"o"=>"global_pages.page"
			),
			
			"category"=>array(
				"h"=>"Category",
				"o"=>"nf_categories.category"
			),
			"commentCount"=>array(
				"h"=>"Comments",
				"o"=>"commentCount"
			),
			"photosCount"=>array(
				"h"=>"Photos",
				"o"=>"photosCount"
			),
			"filesCount"=>array(
				"h"=>"Files",
				"o"=>"filesCount"
			),
			"locked_fullName"=>array(
				"h"=>"Locked&nbsp;By",
				"o"=>"locked_fullName"
			),
			"locked"=>array(
				"b"=>"locked",
				"h"=>"Locked",
				"o"=>"locked"
			),
			"placed"=>array(
				"b"=>"placed",
				"h"=>"Placed",
				"o"=>"placed"
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

			"none"=> array(
				"n"=> "No Ordering",
				"g"=> "none"
			),
			"stage"=> array(
				"n"=> "Stage",
				"g"=> "stage"
			),
			"category"=> array(
				"n"=> "Category",
				"g"=> "category"
			),
			"author"=> array(
				"n"=> "Authors",
				"g"=> "author"
			),
			"priority"=> array(
				"n"=> "Priority",
				"g"=> "priority"
			),
			"page"=> array(
				"n"=> "Page",
				"g"=> "page"
			)
		);




		$sections = array(
			"provisional"=>array("none","stage","author","category","priority","page"),
			"newsbook"=>array("none","stage","author","category","priority","page"),
			"production"=>array("none","author","category","priority","page"),
		
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
					"categoryID"=>array()
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
						"g"=> "author",
						"o"=> "ASC"
					),
					"order"      => array(
						"c"=> "title",
						"o"=> "ASC"
					),
					"count"      => count($defColumns),
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