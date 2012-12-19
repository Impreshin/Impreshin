<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace controllers\nf\save;
use \F3 as F3;
use \Axon as Axon;
use \timer as timer;
use \models\nf as models;
use \models\user as user;


class articles extends save {
	function __construct() {

		$user = F3::get("user");
		$userID = $user['ID'];
		if (!$userID) exit(json_encode(array("error" => F3::get("system")->error("U01"))));

	}



	function form() {
		$user = F3::get("user");
		$userID = $user['ID'];
		$cID = $user['company']['ID'];

		//$accStuff = new models\accounts();
		//$accStuff= $accStuff->get($_POST['accNum']);



		$ID = (isset($_GET['ID'])&&$_GET['ID']&&$_GET['ID'] != "undefined") ? $_GET['ID'] : "";

		$typeID = (isset($_REQUEST['typeID'])) ? $_REQUEST['typeID'] : "";
		$categoryID = (isset($_REQUEST['categoryID'])) ? $_REQUEST['categoryID'] : "";
		$authorID = (isset($_REQUEST['authorID'])) ? $_REQUEST['authorID'] : "";
		$title = (isset($_REQUEST['title'])) ? $_REQUEST['title'] : "";
		$synopsis = (isset($_REQUEST['synopsis'])) ? $_REQUEST['synopsis'] : "";
		$article = (isset($_REQUEST['article'])) ? $_REQUEST['article'] : "";

$typeID = '1';


		$details = new models\articles();
		$details = $details->get($ID);
		$ID = $details['ID'];
		
		$diff_o = percentDiff($details['article_orig'], $article, true);
		$diff_l = percentDiff($details['article'], $article, true);

		$firstStage = models\stages::getAll("cID='$cID'","orderby ASC","0,1");
		$firstStage = $firstStage[0];


		$stageID = ($details['stageID'])? $details['stageID']: $firstStage['ID'];

		$values = array(
			"cID"=>$cID,
			"categoryID"=>$categoryID,
			"authorID"=>$authorID,
			"typeID"=>$typeID,
			"stageID"=>$stageID,
			"title"=>$title,
			"synopsis"=>$synopsis,
			"article"=>$article,
			"percent"=>$diff_l['stats']['percent'],
			"percent_orig"=>$diff_o['stats']['percent']
		);


		if ($stageID == $firstStage['ID']){
			$values['article_orig'] = $article;
		}



		$r = array();
		$r['ID'] = $ID;
		$r['values'] = $values;
		$r['patch'] = $diff_l['patch'];
		/*
		$r['diff'] = array(
			"orig"=> $diff_o,
			"last"=> $diff_l
		);*/
		$r['post'] = $_POST;

		$ID = models\articles::save($ID, $r);
		test_array(array("ID"=>$ID));



	}
	function repeat(){
		$user = F3::get("user");
		$userID = $user['ID'];
		$ID = (isset($_GET['ID'])) ? $_GET['ID'] : "";

		$dID = (isset($_POST['dID']))? $_POST['dID']:"";
		$exact_repeat = (isset($_POST['exact_repeat']))? $_POST['exact_repeat']:"0";


		if ($dID) {
			$response = models\bookings::repeat($ID, $dID, $exact_repeat);
		} else {
			$response = array();
		}



		test_array($response);
		exit();
	}


}
