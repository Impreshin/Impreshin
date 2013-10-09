<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\ab\controllers\save;


use \timer as timer;
use \apps\ab\models as models;
use \models\user as user;


class admin_accounts_import extends save {
	function __construct() {
		parent::__construct();


	}

	function _save() {
		$user = $this->f3->get("user");
		$pID = $user['publication']['ID'];
		$cID = $user['publication']['cID'];


		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";

		$account = isset($_POST['account']) ? $_POST['account'] : "";
		$accNum = isset($_POST['accNum']) ? $_POST['accNum'] : "";
		$statusID = isset($_POST['statusID']) ? $_POST['statusID'] : "";
		$remark = isset($_POST['remark']) ? $_POST['remark'] : "";
		$publications = isset($_POST['publications']) ? $_POST['publications'] : array();


		$return = array(
			"error" => array(),
			"ID"    => $ID

		);


		//test_array($p);
		$submit = true;

		$accounts = array();

		$post = $_POST;

		$stats = array(
			"added"=>0,
			"edited"=>0
		);
		foreach ($post as $k => $v){

			if ($k=='account-new'){
				//$accounts[] = $v;

				foreach ($_POST[$k] as $acc) {
					$acc = explode("|!|", $acc);
					$accounts[] = array(
						"ID"           => "",
						"account"      => $acc[1],
						"accNum"       => $acc[0],
						"statusID"     => $statusID,
						"publications" => $publications,
						"cID"          => $cID
					);
					$stats['added']++;
				}
			}
			if (substr($k,0,13)=="account-edit-"){
				$ID = str_replace("account-edit-","",$k);
				$acc = explode("|!|", $_POST[$k]);
				$accounts[] = array(
					"ID"           => $ID,
					"account"      => $acc[1],
					"accNum"       => $acc[0],
					"statusID"     => $statusID,
					"publications" => $publications,
					"cID"          => $cID
				);
				$stats['edited']++;
			}

		}






//$values = $values['p']['p'];


		$return['records'] = $stats;
		if ($submit) {
			foreach ($accounts as $account){
				$ID = models\accounts::save($account['ID'], $account);
			}



		}


		//	test_array(array("ID"=>$ID,"values"=>$values,"result"=>$return));


		return $GLOBALS["output"]['data'] = $return;

	}



}
