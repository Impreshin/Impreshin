<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace controllers\ab;
use \F3 as F3;
use \timer as timer;
use \models\ab as models;
class controller_reports_production_figures {
	function __construct() {

	}
	function page() {
		$user = F3::get("user");
		$uID = $user['ID'];
		$pID = $user['pID'];
		$cID = $user['publication']['cID'];

		$section = "reports_production";
		$settings = models\settings::_read($section);
		$settings_pub = isset($settings["pub_$pID"])?$settings["pub_$pID"]:array("pubs"=>"");

	//	test_array($settings);


		if ($user['su'] == '1') {
			$publications = models\publications::getAll("cID = '$cID'", "publication ASC");
		} else {
			$publications = models\publications::getAll_user("uID='$uID' AND cID = '$cID'", "publication ASC");
		}
		$p = array();
		$publicationselected = array();
		$pubstr = array();
		$settings_pubs = (isset($settings_pub['pubs']))?explode(",", $settings_pub['pubs']):array();
		foreach ($publications as $pub){
			$pub['selected']='0';
			$pub['disabled']='0';
			if ($user['publication']['ID']==$pub['ID']){
				$pub['selected']='1';
				$pub['disabled']='1';
			}
			if (in_array($pub['ID'], $settings_pubs)){
				$pub['selected'] = '1';
			}
			$p[] = $pub;
			if ($pub['cID'] == $cID) $pubstr[] = $pub['ID'];
			if ($pub['selected']=='1')$publicationselected[] = $pub['publication'];
		}
		$publications = $p;

		$publicationselected = (count($publicationselected)>1)?count($publicationselected)." Publications": $publicationselected[0];



//test_array($settings);

		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","ui/ab/");
		$tmpl->page = array(
			"section"=> "reports",
			"sub_section"=> "production_figures",
			"template"=> "page_reports_production_figures",
			"meta"    => array(
				"title"=> "AdBooker - Reports - Production Figures",
			)
		);
		$tmpl->list = models\production::getAll("cID = $cID", "production ASC");
		$tmpl->selected = (isset($settings['ID']["cID_$cID"])) ? $settings['ID']["cID_$cID"] : "";



		$tmpl->publications = $publications;
		$tmpl->publicationselected = $publicationselected;


		$tmpl->settings = $settings;
		$tmpl->output();

	}

}
