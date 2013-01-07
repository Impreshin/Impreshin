<?php
/*
 * Date: 2011/10/31
 * Time: 5:06 PM
 */
namespace controllers\ab;
use \F3 as F3;
use \timer as timer;
use \models\ab as models;
class controller_reports_publication_discounts {
	function __construct() {
		$this->f3 = \base::instance();
	}
	function page() {
		$user = $this->f3->get("user");
		$uID = $user['ID'];
		$pID = $user['pID'];
		$cID = $user['publication']['cID'];

		$section = "reports_publication";
		$settings = models\settings::_read($section);
		$settings_pub = isset($settings["pub_$pID"])?$settings["pub_$pID"]:array("pubs"=>"");

		$s = models\settings::settings();
		$s = $s['columns']['percent_diff'];
		$settings['col'][] = $s;
		$settings['count'] = count($settings['col']);
		//test_array($settings);


		if ($user['su'] == '1') {
			$publications = models\publications::getAll("cID = '$cID'", "publication ASC");
		} else {
			$publications = models\publications::getAll_user("ab_users_pub.uID='$uID' AND global_publications.cID = '$cID'", "publication ASC");
		}
		$p = array();
		$publicationselected = array();
		$settings_pubs = (isset($settings_pub['pubs']))?explode(",", $settings_pub['pubs']):array();
		$pubstr = array();
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
			if ($pub['cID']==$cID) $pubstr[] = $pub['ID'];
			if ($pub['selected']=='1')$publicationselected[] = $pub['publication'];
		}
		$publications = $p;

		$publicationselected = (count($publicationselected)>1)?count($publicationselected)." Publications": $publicationselected[0];



//test_array($settings);

		//test_array($ab_settings);
		$tmpl = new \template("template.tmpl","ui/ab/");
		$tmpl->page = array(
			"section"=> "reports",
			"sub_section"=> "publication_discounts",
			"template"=> "page_reports_publication_discounts",
			"meta"    => array(
				"title"=> "AB - Reports - Publication Discounts",
			)
		);
		$pubstr = implode(",", $pubstr);



		$tmpl->publications = $publications;
		$tmpl->publicationselected = $publicationselected;


		$selected = (isset($settings['ID']["cID_$cID"])) ? $settings['ID']["cID_$cID"] : "";


		//test_array(models\settings::_read($section));
		$tmpl->settings = $settings;
		$tmpl->selected = $selected;
		$tmpl->output();

	}

}
