<?php
/*
 * Date: 2011/11/16
 * Time: 11:16 AM
 */
namespace controllers;
class controller_screenshots {
	function __construct(){
		$this->f3 = \base::instance();
	}
	function page(){
		
		$app = $this->f3->get("PARAMS['app']");
		
		$apps = $this->f3->get("applications");



		$screenshots = array();
		$screenshots[] = array(
			"i"=>"provisional_list.png",
			"n"=>"Provisional List",
			"d"=>"the main list"
		);
		$screenshots[] = array(
			"i"=>"production_list.png",
			"n"=>"Production List",
			"d"=>"with production specifics"
		);
		$screenshots[] = array(
			"i"=>"layout.png",
			"n"=>"Layout",
			"d"=>"drag adverts from right onto pages"
		);
		$screenshots[] = array(
			"i"=>"details.png",
			"n"=>"Record Details",
			"d"=>""
		);
		
		$screenshots[] = array(
			"i"=>"details_logs.png",
			"n"=>"Record Details Logs",
			"d"=>"all actions are logged"
		);
		
		$screenshots[] = array(
			"i"=>"details_material.png",
			"n"=>"Record uploading material",
			"d"=>"Material status form"
		);
		
		$screenshots[] = array(
			"i"=>"search_dates.png",
			"n"=>"Record Search",
			"d"=>"showing the date options"
		);
		
		$screenshots[] = array(
			"i"=>"form_accounts.png",
			"n"=>"Record Capture",
			"d"=>"the accounts list"
		);
		
		$screenshots[] = array(
			"i"=>"overview.png",
			"n"=>"Overview",
			"d"=>"Edition Overview"
		);
		$screenshots[] = array(
			"i"=>"admin_accounts.png",
			"n"=>"Admin Accounts",
			"d"=>""
		);
		$screenshots[] = array(
			"i"=>"admin_targets.png",
			"n"=>"Admin Marketer Targets",
			"d"=>""
		);
		$screenshots[] = array(
			"i"=>"admin_users.png",
			"n"=>"Admin Users",
			"d"=>""
		);
		$screenshots[] = array(
			"i"=>"reports_marketer_figures_1.png",
			"n"=>"Reports Figures",
			"d"=>""
		);
		$screenshots[] = array(
			"i"=>"reports_marketer_figures_2.png",
			"n"=>"Reports Charts",
			"d"=>""
		);
		
		
		$images = array();

		$images["ab"]=array(
			"app"=>"ab",
			"name"=>$apps['ab']['name'],
			"description"=>$apps['ab']['description'],
			"screenshots"=>$screenshots
		);

		$screenshots = array();
		$screenshots[] = array(
			"i"=>"articles.png",
			"n"=>"Main article list",
			"d"=>""
		);
		$screenshots[] = array(
			"i"=>"newsbook.png",
			"n"=>"Newsbook",
			"d"=>"Displays the current newsbook's progress"
		);
		$screenshots[] = array(
			"i"=>"layout.png",
			"n"=>"Layout",
			"d"=>"Drag articles onto their pages"
		);
		$screenshots[] = array(
			"i"=>"details.png",
			"n"=>"Article Details",
			"d"=>""
		);
		$screenshots[] = array(
			"i"=>"details_history.png",
			"n"=>"Article Details",
			"d"=>"All changes are stored and are clearly visible"
		);
		$screenshots[] = array(
			"i"=>"details_photo.png",
			"n"=>"Article Details",
			"d"=>"Photos display their EXIF and are zoom'able"
		);


		$screenshots[] = array(
			"i"=>"production.png",
			"n"=>"Production",
			"d"=>""
		);
		$screenshots[] = array(
			"i"=>"records_search.png",
			"n"=>"Search",
			"d"=>""
		);
		$screenshots[] = array(
			"i"=>"records_newsbook.png",
			"n"=>"Newsbook Archives",
			"d"=>"Lists records based on the newsbooks they were added to"
		);
		
		
		
		$screenshots[] = array(
			"i"=>"reports_author_submitted_top.png",
			"n"=>"Reports",
			"d"=>"Author submitted"
		);
		$screenshots[] = array(
			"i"=>"reports_author_submitted_bottom.png",
			"n"=>"Reports",
			"d"=>"Author submitted"
		);
		$screenshots[] = array(
			"i"=>"reports_author_submitted_bottom2.png",
			"n"=>"Reports",
			"d"=>"Author submitted"
		);
		$screenshots[] = array(
			"i"=>"reports_category_figures_top.png",
			"n"=>"Reports",
			"d"=>"Category Figures"
		);
		$screenshots[] = array(
			"i"=>"reports_category_figures_records.png",
			"n"=>"Reports",
			"d"=>"Category Figures Records"
		);
		$screenshots[] = array(
			"i"=>"reports_category_figures_records.png",
			"n"=>"Reports",
			"d"=>"Category Figures Records"
		);
		$screenshots[] = array(
			"i"=>"admin_style.png",
			"n"=>"Admin",
			"d"=>"Style sheet - for working out the cm"
		);
		
		$screenshots[] = array(
			"i"=>"admin_users_top.png",
			"n"=>"Admin",
			"d"=>"Users"
		);
		
		$screenshots[] = array(
			"i"=>"admin_users_bottom.png",
			"n"=>"Admin",
			"d"=>"Users - Permissions"
		);
		$screenshots[] = array(
			"i"=>"form_top.png",
			"n"=>"Form",
			"d"=>""
		);
		
		$screenshots[] = array(
			"i"=>"form_bottom.png",
			"n"=>"Form",
			"d"=>""
		);
		
		
		
		
		
		
		
		
		
		
		$images["nf"]=array(
			"app"=>"nf",
			"name"=>$apps['nf']['name'],
			"description"=>$apps['nf']['description'],
			"screenshots"=>$screenshots
		);
		
		
		
		
		
		$data= isset($images[$app])?$images[$app]:false;

		//test_array($images['ab']); 
		$tmpl = new \template("template.tmpl", "ui/front/", true);
		$tmpl->page = array(
			"section"    => "screenshots",
			"sub_section"=> "form",
			"template"   => "page_screenshots",
			"meta"       => array(
				"title"=> "ScreenShots",
			)
		);
		$tmpl->images = $images;
		$tmpl->data = $data;
		$tmpl->apps = $images;
		$tmpl->output();

	}


}
