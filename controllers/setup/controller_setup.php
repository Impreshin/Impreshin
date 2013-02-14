<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace controllers\setup;
use models\nf\publications;
use \timer as timer;
use \models\user as user;
class controller_setup {
	function __construct() {
		$this->f3 = \base::instance();
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		if (!$userID) $this->f3->reroute("/login");
		$this->cID = $this->f3->get('PARAMS["company"]');
		$this->pID = $this->f3->get('PARAMS["pID"]');
		$this->app = $this->f3->get('PARAMS["app"]');
		$this->section = $this->f3->get('PARAMS["section"]');
		$setup = array();
		include_once("setup.php");
		$this->wizard_data = $setup;
	}
	function page() {

		$timer = new timer();
		$user = $this->f3->get("user");
		//$this->f3->get("DB")->exec("UPDATE global_users SET last_page = '" . $_SERVER['REQUEST_URI'] . "' WHERE ID = '" . $user['ID'] . "'");


		$companyO = new \models\company();
		$company = $companyO->get($this->cID);
		$publicationO = new \models\publications();
		$publication = $publicationO->get($this->pID);




		$base_url = "/setup/" . $company['ID'] . "/" . $this->app . "/" . $publication['ID'] . "/";


		$data = $this->data();

		$data['page']['next'] = $data['page']['next']? $base_url . $data['page']['next']: false;
		$data['page']['prev'] = $data['page']['prev']? $base_url . $data['page']['prev']: $base_url;









		//test_array($settings);

		$tmpl = new \template("template.tmpl",array("ui/setup/","ui/setup/".$this->app."/"));
		$tmpl->page = array(
			"section"=> $this->section,
			"sub_section"=> "",
			"template"=> $data['page']['page'],
			"meta"    => array(
				"title"=> "Setup - ". $data['page']['heading'],
			)
		);
		$tmpl->it = array(
			"prev" => $data['page']['prev'],
			"next" => $data['page']['next']
		);
		$tmpl->base_url = $base_url;

		$tmpl->heading = $data['page']['heading'];
		$tmpl->section = $this->section;
		$tmpl->nav = $data['nav'];
		$tmpl->data = $data['page'];
		$tmpl->company = $company;
		$tmpl->publication = $publication;
		$tmpl->application = $this->app;
		$tmpl->output();
		$timer->stop("Controller - ".__CLASS__." - ".__FUNCTION__, func_get_args());
	}
	function data(){
		$setup = $this->wizard_data;
		$nav = array();
		$contents = $setup;

		if (!isset($setup[$this->app])) $this->f3->error(404);
		$setup = $setup[$this->app];
		if (!isset($setup[$this->section])) $this->f3->error(404);






		$pages = array();
		$active = true;
		foreach ($setup as $k => $v){
			$pages[] = $k;


			$nav[] = array(
				"link_heading"=>$v['link_heading'],
				"active"=>$active?"1":"0",
				"section"=>$k
			);
			if ($this->section == $k) $active = false;
		}

		$current_index = array_search($this->section,$pages);
		$next = (isset($pages[$current_index + 1]))? $pages[$current_index + 1] : false;
		$prev = (isset($pages[$current_index - 1]))? $pages[$current_index -1] : false;



		$data = $setup[$this->section];
		$data['section'] = (isset($pages[$current_index])) ? $pages[$current_index] : false;;
		$data['prev'] = $prev;
		$data['next'] = $next;






		$return = array(
			"page"=> $data,
			"nav"=>$nav
		);



		return $return;
	}





}
