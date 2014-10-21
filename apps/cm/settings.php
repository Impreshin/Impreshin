<?php

namespace apps\cm;

use \timer as timer;

class settings {
	function __construct() {


	}

	public static function _available($permissions=array(),$section='general') {
		$timer = new timer();
		$f3 = \Base::instance();
		$return = array();

		$user = $f3->get("user");
		/*
		 * name => array(
		 *  h =>"heading",
		 *  o =>"order by",
		 *  d =>"description",
		 *  b =>"show a tick mark if its 1",
		 *  m => "minimum width",
		 *  mw => "maximum width",
		 *  w => "width",
		 * )
		 */

		/////////////////////// companies ////////////////////////
		$columns = array(
			"company"                 => array(
				"h"=> "Company",
				"m"=>"180"
			),
			"short"                 => array(
				"h"=> "Co.",
				"d"=>"Short name for the company",
			),
		    "phone"=>array(
			    "h"=>"Phone"
		    ),
		    "fax"=>array(
			    "h"=>"Fax"
		    ),
		    "email"=>array(
			    "h"=>"Email"
		    ),
		    "website"=>array(
			    "h"=>"Website"
		    ),
		    "address1"=>array(
			    "h"=>"Address&nbsp;1"
		    ),
		    "address2"=>array(
			    "h"=>"Address&nbsp;2"
		    ),
		    "city"=>array(
			    "h"=>"City"
		    ),
		    "country"=>array(
			    "h"=>"Country"
		    ),
		    "postalCode"=>array(
			    "h"=>"PostCode"
		    ),
		    "taxID"=>array(
			    "h"=>"Tax&nbsp;nr"
		    ),

		);


		
		$c = array();
		foreach ($columns as $column=>$values){
			$values['c'] = $column;
			if (!isset($values['o']))$values['o'] = "`".$column."`";
			$c[$column] = $values;
		}


		$return['companies']["columns"] = $c;

		/*
		 * name => array(
		 *  n=>"Heading",
		 *  g=>"group by"
		 * )
		 */
		$groupByoptions = array(
			"az"=>array(
				"n"=> "A-Z",
				"g"=> "az"
			),
			"none"=> array(
				"n"=> "No Grouping",
				"g"=> "none"
			)
			
		);




		$sections = array(
			"companies"=>array("az","none"),
		
		);

		$groupby = array();
		foreach ($sections as $key=>$value){
			$opts = array();
			foreach($value as $col){
				$opts[] = $groupByoptions[$col];
			}
			$groupby[$key] = $opts;
		}



		$return['companies']["groupby"] = $groupby;


		/////////////////////// contacts ////////////////////////


		$columns = array(
			"firstName"                 => array(
				"h"=> "First&nbsp;Name",
			),
			"lastName"                 => array(
				"h"=> "Last&nbsp;Name",
			),
			"fullName"                 => array(
				"h"=> "Full&nbsp;Name",
			    "o"=>"`cm_contacts`.`firstName`"
			),
			"phone"=>array(
				"h"=>"Phone"
			),
			"fax"=>array(
				"h"=>"Fax"
			),
			"email"=>array(
				"h"=>"Email"
			),
			"website"=>array(
				"h"=>"Website"
			),
			"address1"=>array(
				"h"=>"Address&nbsp;1"
			),
			"address2"=>array(
				"h"=>"Address&nbsp;2"
			),
			"city"=>array(
				"h"=>"City"
			),
			"country"=>array(
				"h"=>"Country"
			),
			"postalCode"=>array(
				"h"=>"PostCode"
			),

		);



		$c = array();
		foreach ($columns as $column=>$values){
			$values['c'] = $column;
			if (!isset($values['o']))$values['o'] = "`".$column."`";
			$c[$column] = $values;
		}


		$return['contacts']["columns"] = $c;

		/*
		 * name => array(
		 *  n=>"Heading",
		 *  g=>"group by"
		 * )
		 */
		$groupByoptions = array(
			"az"=>array(
				"n"=> "A-Z",
				"g"=> "az"
			),
			"last_az"=>array(
				"n"=> "A-Z (Last Name)",
				"g"=> "az_last"
			),
			"none"=> array(
				"n"=> "No Grouping",
				"g"=> "none"
			)

		);




		$sections = array(
			"contacts"=>array("az","last_az","none"),

		);

		$groupby = array();
		foreach ($sections as $key=>$value){
			$opts = array();
			foreach($value as $col){
				$opts[] = $groupByoptions[$col];
			}
			$groupby[$key] = $opts;
		}



		$return['contacts']["groupby"] = $groupby;


		

		$return['general']=array(
			"types"=>array(
				array(
					"ID"=>"1",
					"group"=>"Phones",
				    "type"=>"Mobile",
				    "icon"=>"icon-phone",
				    "orderby"=>1
				),
				array(
					"ID"=>"2",
					"group"=>"Phones",
				    "type"=>"Work",
				    "icon"=>"icon-phone",
				    "orderby"=>2
				),
				array(
					"ID"=>"3",
					"group"=>"Phones",
				    "type"=>"Home",
				    "icon"=>"icon-phone",
				    "orderby"=>3
				),
				array(
					"ID"=>"4",
					"group"=>"Phones",
				    "type"=>"Main",
				    "icon"=>"icon-phone",
				    "orderby"=>4
				),
				array(
					"ID"=>"5",
					"group"=>"Phones",
				    "type"=>"Other",
				    "icon"=>"icon-phone",
				    "orderby"=>5
				),
				array(
					"ID"=>"6",
					"group"=>"Fax",
				    "type"=>"Work",
				    "icon"=>"",
				    "orderby"=>6
				),
				array(
					"ID"=>"7",
					"group"=>"Fax",
				    "type"=>"Home",
				    "icon"=>"",
				    "orderby"=>7
				),
				array(
					"ID"=>"8",
					"group"=>"",
				    "type"=>"Pager",
				    "icon"=>"",
				    "orderby"=>110
				),
				array(
					"ID"=>"9",
					"group"=>"",
				    "type"=>"Website",
				    "icon"=>"",
				    "orderby"=>101
				),
				array(
					"ID"=>"10",
					"group"=>"Address",
				    "type"=>"Address 1",
				    "icon"=>"",
				    "orderby"=>10
				),
				array(
					"ID"=>"11",
					"group"=>"Address",
				    "type"=>"Address 2",
				    "icon"=>"",
				    "orderby"=>11
				),
				array(
					"ID"=>"12",
					"group"=>"Address",
				    "type"=>"City/Town",
				    "icon"=>"",
				    "orderby"=>12
				),
				array(
					"ID"=>"13",
					"group"=>"Address",
				    "type"=>"Suburb",
				    "icon"=>"",
				    "orderby"=>13
				),
				array(
					"ID"=>"14",
					"group"=>"",
				    "type"=>"Email",
				    "icon"=>"icon-envelope",
				    "orderby"=>100
				),
				array(
					"ID"=>"15",
					"group"=>"Address",
				    "type"=>"Postal Code",
				    "icon"=>"",
				    "orderby"=>14
				),
			),
		    "interaction_types_icons"=>array(
			    "1"=>"icon-phone",
			    "2"=>"icon-envelope",
			    "3"=>"icon-comments-alt",
			    "4"=>"icon-mobile-phone",
			    "5"=>"icon-bookmark-empty",
		    )
		);
	

		usort($return['general']['types'], function($a, $b) {
				return $a['orderby'] - $b['orderby'];
			});
		
		
		//test_array($return); 
		
		
//test_array(array("section"=>$section,"is"=>isset($return[$section]),"return"=>$return)); 

		
		if ($section && @isset($return[$section])) {
			$return = $return[$section];
		} else {
			
		}
	


		
		

		$cfg = $f3->get("CFG");




		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function defaults($application = "cm", $ID = "") {
		$timer = new timer();
		$f3 = \Base::instance();
		$return = array();
		
		
		
		
			$settings = array(
				"details"=>array(
					"interactions"=>array(
						"typeID"=>"1"
					)
				),
				"form"=>array(
					"type"=>"co"
				),
				"front"=>array(
					
				),
				"calendar"=>array(
					
				),
				"companies"=>array(
					"col"        => array(
						"company",
						"short",
						"phone",
						"fax",
						"email",
					    "website"
					),
					"group"      => array(
						"g"=>"az",
						"o"=>"ASC"
					),
					"order"      => array(
						"c"=> "company",
						"o"=> "ASC"
					),
					"count"      => "6",
					"highlight"=> "",
					"filter"   => "*",
					"search" => ""
				),
				"contacts"=>array(
					"col"        => array(
						"title",
						"fullName",
						"phone",
						"fax",
						"email",
						"website"
						
					),
					"group"      => array(
						"g"=>"az_last",
						"o"=>"ASC"
					),
					"order"      => array(
						"c"=> "lastName",
						"o"=> "ASC"
					),
					"count"      => "6",
					"highlight"=> "",
					"filter"   => "*",
					"search" => ""
				),
				

			);

		$return =$settings;



		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}



}