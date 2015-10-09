<?php

namespace apps\nf;


use \timer as timer;

class permissions {
	function __construct() {


	}

	public static function _available($cID="") {
		$timer = new timer();
		$f3 = \Base::instance();
		$return = array();
		$return['p'] = array(
			"view"=> array(
				"only_my_records"=>0
			),
			"stages"=>array(),
			"form"           => array(
				"new"         => 0,
				"edit_master" => 0,
				"author_dropdown"=>0,
				"priority"=>0
			),
			"details"           => array(
				"overwrite_locked"         => 0,
				"placed"         => 0,
				"archive"         => 0,
				"stage_jump_list"=>0,
				
				
			),
			"articles"         => array(
				"checkbox_archive"      => 0,
				"checkbox_add_to_newsbook" => 0
			),
			"newsbook"         => array(
				"page"      => 0,
			),
			"layout"         => array(
				"page"      => 0,
				"pagecount" => 0,
				"editpage"  => 0
			),
			"production"         => array(
				"page"      => 0,
				"upload_page_pdf"=>0
			),
			"records"        => array(
				"deleted" => array(
					"page" => 0,
				),
				"search"  => array(
					"page" => 0
				),
				"newsbook"  => array(
					"page" => 0
				)
				
			),
			"reports"=>array(
				"author"=> array(
					"submitted"  => array(
						"page" => 0
					),
					"newsbook"  => array(
						"page" => 0
					)	
				),
				"category" => array(
					"figures"   => array(
						"page" => 0
					),
				),
				"publication" => array(
					"figures"   => array(
						"page" => 0
					),
				)
			),
			"administration" => array(
				"application" => array(
					"checklists"=>array(
						"page"=>0
					),
					"priorities"=>array(
						"page"=>0
					),
					"categories"=>array(
						"page"=>0
					),
					"stages"=>array(
						"page"=>0
					),
					"sections"=>array(
						"page"=>0
					),
					"cm_style_sheet"=>array(
						"page"=>0
					),
					"loading"=>array(
						"page"=>0
					),
					"resources"=>array(
						"page"=>0
					),
					"dictionary"=>array(
						"page"=>0
					),
					
				),
				"system"      => array(
					"dates"        => array(
						"page" => 0
					),
					"users"        => array(
						"page" => 0
					),
					"publications" => array(
						"page" => 0
					)
				)
			)

		);


		$return['d'] = array(
			
			"view"=>array(
				'only_my_records'=>"If this is checked checked then the user will ONLY see their own records (author ID)."
			),
			"form"=>array(
				"priority"=>"This user will be able to use the priority slider on the form to set the articles priority"	
			),
			"records"=>array(
				"mass_archive"  =>  "A page with checkboxes to allow mass archiving of records"
				
			),
			"administration" => array(

				"system"      => array(
					"dates"        => array(
						"page" => ""
					),
					"users"        => array(
						"page" => ""
					),
					"publications" => array(
						"page" => ""
					)
				)
			)
		);
		
		if ($cID){
			$stages = models\stages::getAll("cID='". $cID."' OR cID='0'","orderby ASC");

			$perms = array();
			$perms_desc = array();

			foreach ($stages as $item){
				$perms[$item['ID']] = array(
					"label"=>$item['stage'],
					"edit"=>"0",
					"to"=>"0",
					"reject"=>"0",
					"delete"=>"0",
					"newsbook"=>"0",
					"send_to_lin"=>"0"
				);
				$perms_desc[$item['ID']] = array(
					"edit"=>"Allows the user to edit the record whilst the booking is in the ".$item['stage']." stage.",
					"to"=>"Allows the user to move a record into the ".$item['stage']." stage.",
					"reject"=>"Allows the user to reject the record in this stage",
					"delete"=>"Allows the user to delete the record whilst the booking is in the ".$item['stage']." stage.",
					"newsbook"=>"Allows the user to add the booking to a newsbook while in this stage",
					"send_to_lin"=>"Adds a button to allow the user to send the article to the lin framework"
				);
			}


			//test_array($stage_permissions); 
			$return['p']['stages']=$perms;
			$return['d']['stages']=$perms_desc;
		}

		


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}
	public static function defaults($cID="") {
		$timer = new timer();

		$return = self::_available($cID);;



		$timer->stop(array("Models"=>array("Class"=> __CLASS__ , "Method"=> __FUNCTION__)), func_get_args());
		return $return;
	}



}