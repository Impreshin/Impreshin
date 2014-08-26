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
			"i"=>"details.jpg",
			"n"=>"Record Details",
			"d"=>""
		);
		
		$screenshots[] = array(
			"i"=>"details_logs.png",
			"n"=>"Record Details Logs",
			"d"=>"all actions are logged"
		);
		
		$screenshots[] = array(
			"i"=>"details_material.jpg",
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
			"i"=>"details_comments.png",
			"n"=>"Article Details",
			"d"=>"Comments"
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
		$screenshots[] = array(
			"i"=>"meta_search_1.png",
			"n"=>"Meta Search",
			"d"=>"Click the meta search button to do a search if the article is used anywhere"
		);
		$screenshots[] = array(
			"i"=>"meta_search_2.png",
			"n"=>"Meta Search",
			"d"=>"This article returns some hits"
		);
		$screenshots[] = array(
			"i"=>"meta_search_3.png",
			"n"=>"Meta Search",
			"d"=>"Looks legit..."
		);
		
		
		
		
		
		
		
		
		
		
		$images["nf"]=array(
			"app"=>"nf",
			"name"=>$apps['nf']['name'],
			"description"=>$apps['nf']['description'],
			"screenshots"=>$screenshots
		);
		
		
		
		$images["cm"]=array(
			"app"=>"cm",
			"name"=>$apps['cm']['name'],
			"description"=>$apps['cm']['description'],
			"screenshots"=>array()
		);
		
		$images["pf"]=array(
			"app"=>"pf",
			"name"=>$apps['pf']['name'],
			"description"=>$apps['pf']['description'],
			"screenshots"=>array()
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
	
	public function thumb() {
		$f3 = \base::instance();
		$cfg = $f3->get("CFG");
		$f3->set("json",False);

		$cfg = $this->f3->get("CFG");


		
		
		$folder ="./front/screenshots/";


		//header("Content-Type: image/png");
		//header('Cache-control: max-age=' . (60 * 60 * 24 * 365));
		//header('Expires: ' . gmdate(DATE_RFC1123, time() + 60 * 60 * 24 * 365));
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
			//	header('HTTP/1.1 304 Not Modified');
			die();
		}

		$filename = isset($_REQUEST['file'])?$_REQUEST['file']:"";
		$crop = isset($_REQUEST['crop'])?$_REQUEST['crop']:true;

		if ($crop==="false"){
			$crop=false;
		}

		$file = $folder . $filename;
		$file = $f3->fixslashes($file);
		$file = str_replace("//","/",$file);


		//test_array(array("file"=>$file,"exists"=>file_exists($file)));

		

		$width = isset($_REQUEST['w'])?$_REQUEST['w']:"";
		$height = isset($_REQUEST['h'])?$_REQUEST['h']:"";


		//test_array($file);

		if (file_exists($file)) {
			$thumb = new \mods_Image($file);

			$thumb->resize($width, $height, $crop);
			$thumb->render();

		}


	}

}
