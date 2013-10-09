<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace apps\nf\controllers\admin;

use \timer as timer;
use \apps\nf\models as models;
class checklists extends \apps\nf\controllers\_ {
	function __construct() {
		parent::__construct();
	}
	function page() {
		$user = $this->f3->get("user");
		$userID = $user['ID'];

		$cID = $user['company']['ID'];



//test_array($pages);

		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","apps/nf/ui/");
		$tmpl->page = array(
			"section"=> "admin",
			"sub_section"=> "checklists",
			"template"=> "admin_checklists",
			"meta"    => array(
				"title"=> "NF - Admin - Checklists",
			), 
			"js" => array("/ui/ckeditor/ckeditor.js"),
			"css" => array(),
		);

		$tmpl->categories = models\categories::getAll("cID='" . $cID . "'", "orderby ASC");
		$tmpl->output();

	}

}
