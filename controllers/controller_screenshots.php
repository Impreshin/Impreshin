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
