<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace controllers\setup;
use models\company;
use models\nf\publications;
use \timer as timer;
use \models\user as user;

class controller_home {
	function __construct() {
		$this->f3 = \base::instance();
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		if (!$userID) $this->f3->reroute("/login");
		$this->cID = $this->f3->get('PARAMS["company"]');
		$this->app = $this->f3->get('PARAMS["app"]');
		$this->pID = $this->f3->get('PARAMS["pID"]');
	}

	function page() {

		$timer = new timer();
		$user = $this->f3->get("user");
		//$this->f3->get("DB")->exec("UPDATE global_users SET last_page = '" . $_SERVER['REQUEST_URI'] . "' WHERE ID = '" . $user['ID'] . "'");
		$app = $this->app;

		if (isset($_GET['cID'])){
			$this->cID = $_GET['cID'];
		}
		$companyO = new \models\company();
		$company = $companyO->get($this->cID);


		$publicationO = new \models\publications();
		$publication = $publicationO->get($this->pID);


		$setup = array();
		$nav = array();
		include_once("setup.php");

		if ($this->app){
			if (isset($setup[$this->app])) {
				$setup = $setup[$this->app];

				foreach ($setup as $k => $v) {
					$nav[] = array(
						"link_heading" => $v['link_heading'],
						"active"       => "0",
						"section"      => $k
					);
				}
			} else {
				$this->f3->error(404);

			}
		}





		//test_array($_POST);
		$error = array();
		if (isset($_GET['save']) && count($_POST)) {
			$publication_name = Isset($_REQUEST['publication']) ? $_REQUEST['publication'] : "";
			$columnsav = Isset($_REQUEST['columnsav']) ? $_REQUEST['columnsav'] : "";
			$cmav = Isset($_REQUEST['cmav']) ? $_REQUEST['cmav'] : "";
			$pagewidth = Isset($_REQUEST['pagewidth']) ? $_REQUEST['pagewidth'] : "";
			$printOrder = Isset($_REQUEST['printOrder']) ? $_REQUEST['printOrder'] : "";

			if (!$publication_name) $error[] = "Publication not specified";
			if (!$columnsav || !is_numeric($columnsav)) $error[] = "Page Columns not specified or not numeric";
			if (!$cmav || !is_numeric($cmav)) $error[] = "Page cm not specified or not numeric";
			if (!$pagewidth || !is_numeric($pagewidth)) $error[] = "Page width not specified or not numeric";
			if (!$printOrder || !is_numeric($printOrder)) $error[] = "Print Order not specified or not numeric";

			$publication['publication'] = $publication_name;
			$publication['columnsav'] = $columnsav;
			$publication['cmav'] = $cmav;
			$publication['pagewidth'] = $pagewidth;


			if (!count($error)) {
				$values = array(
					"cID"=>$company['ID'],
					"publication" => $publication_name,
					"columnsav"   => $columnsav,
					"cmav"        => $cmav,
					"pagewidth"   => $pagewidth,
					"printOrder"   => $printOrder
				);
					$t = \models\publications::save($publication['ID'], $values, $company['ID']);
				$this->f3->reroute("/setup/".$company['ID']."/".$app."/".$t."/". $nav[0]['section']);

			}


		}
		if (isset($_GET['savecompany']) && count($_POST)) {
			$company_name = Isset($_REQUEST['company']) ? $_REQUEST['company'] : "";


			if (!$company_name) $error[] = "Company not specified";



			if (!count($error)) {
				$values = array(

					"company" => $company_name,
					"ab"   => '1',
					"nf"        => '1',
				);
				$t = \models\company::save($company['ID'], $values);
				$this->f3->reroute("/setup/" . $t);

			}


		}

		$first = "";
		$section = "home";
		$previous = "";
		$next = "";

		$show = "company_list";
		$heading = "Please select a company";


		if ($company['ID'] && !isset($_GET['cID'])) {
			$section = "app";
			$previous = "/setup/";
			$show = "application_list";
			$heading = "Please select an application";
		}
		if ($app) {
			$section = "pub";
			$previous = "/setup/" . $company['ID'];
			$show = "publication_list";
			$heading = "Please select a publication or add a new one";

			$first = ($nav[0]['section']);
		}
		if ($publication['ID']) {
			$section = "pub";
			$previous = "/setup/" . $company['ID'] . "/" . $app;
			$next = "/setup/" . $company['ID'] . "/" . $app . "/" . $publication['ID'] . "/". $first;
			$show = "publication_details";
			$heading = "Edit publication details or click next";
		}


		if ($user['su'] == '1') {
			$companies = \models\company::getAll();
		} else {
			$companies = \models\company::getAll_user("global_users_company.uID='" . $user['ID'] . "' and allow_setup ='1'");
		}

		//test_array($companies);


		$tmpl = new \template("template.tmpl", "ui/setup/", true);
		$tmpl->page = array(
			"section"     => $section,
			"sub_section" => "",
			"template"    => "page_home",
			"meta"        => array(
				"title" => "Setup - Home",
			)
		);


		$tmpl->application = $this->app;

		$tmpl->company = $company;
		$tmpl->publication = $publication;



		$tmpl->list = array(
			"companies"    => $companies,
			"publications" => \models\publications::getAll("cID = '" . $company['ID'] . "'")
		);

		$tmpl->it = array(
			"prev" => $previous,
			"next" => $next,
			"first"=>$first
		);
		$tmpl->show = $show;
		$tmpl->heading = $heading;
		$tmpl->errors = $error;

		$tmpl->nav = $nav;

		$tmpl->output();
		$timer->stop("Controller - " . __CLASS__ . " - " . __FUNCTION__, func_get_args());
	}


}
