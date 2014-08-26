<?php

namespace apps\cm;

use \timer as timer;

class settings {
	function __construct() {


	}

	public static function _available($permissions=array()) {
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

		$columns = array(
			

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

			
		);




		$sections = array(
			
		
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


		
		

		$cfg = $f3->get("CFG");




		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function defaults($application = "cm", $ID = "") {
		$timer = new timer();
		$f3 = \Base::instance();
		$return = array();
		
		
		
		
			$settings = array(
				"front"=>array(
					
				),
				"calendar"=>array(
					
				),
				"companies"=>array(
					
				),
				"people"=>array(
					
				),
				

			);

		$return =$settings;



		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}



}