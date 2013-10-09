<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\ab\controllers\data;

use \timer as timer;
use \apps\ab\models as models;
use \models\user as user;


class admin_accounts_import extends data {
	function __construct() {
		parent::__construct();

	}



	function _details() {
		$timer = new timer();
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['publication']['ID'];
		$cID = $user['publication']['cID'];

		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";

		$return = array();
		$data = array();

		$options = array(
			"text_csv_deliminator"=>isset($_POST['text_csv_deliminator'])&& $_POST['text_csv_deliminator']?$_POST['text_csv_deliminator']:";",
			"text_csv_enclosed"=>isset($_POST['text_csv_enclosed']) && $_POST['text_csv_enclosed']?$_POST['text_csv_enclosed']:'"',
			"text_csv_escaped"=>isset($_POST['text_csv_escaped']) && $_POST['text_csv_escaped']?$_POST['text_csv_escaped']:'"',
			"text_csv_new_line"=>isset($_POST['text_csv_new_line']) && $_POST['text_csv_new_line']?$_POST['text_csv_new_line']:"auto",
			"text_csv_columns"=>isset($_POST['text_csv_columns'])&& $_POST['text_csv_columns']?$_POST['text_csv_columns']:"accNum, account",
		);
		$newline = $options['text_csv_new_line'];
		if ($options['text_csv_new_line']=='auto'){
			$newline = "\n";
		}
		$data['options'] = $options;


		$records = array();

		$accounts =  models\accounts::getAll("ab_accounts.cID = '$cID'", "accNum ASC, account ASC");

		$a = array();
		foreach ($accounts as $account){
			$a[$account['accNum']] = $account;
		}

		$accounts = $a;

		//test_array($a);
		$demodata = 'Prepared by:  Zoutnet;
Customer Listing;
;
Acc no;Description
A09;KODAC
A110;ADMAKERS INTERNATIONAL (PTY) LTD
A1222;ANHETICO PROPERTIES
A121;AVOCA VALE COUNTRY HOTEL
A131;AFRICA HARDWARE
A17;AUTOZONE - b
A18;ABSOLUTELY WATER LIMPOPO
A26;AYOB MOTORS (AUTOFLAIR CC)
A58;FOREVER RESORTS TSHIPISE
B08;BLOUBERG DIEREKLINIEK - b
B10;BONNEMA HANNETJIE
B100;BRITS DEON';

		$csv = isset($_POST['csv'])? $_POST['csv']: "";

		//$csv = $demodata;

		if ($csv){
			$columns = array();

				foreach(explode(",",$options['text_csv_columns']) as $col){
					$columns[] = trim($col);
				};
		$data['csv'] = $csv;


			$records = array();

			$csvData = str_getcsv($data['csv'], $newline); //parse the rows
			foreach ($csvData as $record) {
				$r = str_getcsv($record, $options['text_csv_deliminator'], $options['text_csv_enclosed'], $options['text_csv_escaped']);
				$i = 0;
				$ret = array();
				$match = array();
				foreach ($r as $v){
					if (isset($columns[$i])) {
						if ($columns[$i]=='accNum'){
							if (isset($accounts[$v])) $match = $accounts[$v];
						}
						$ret[$columns[$i]]= $v;
					}
					$i++;
				}
				$ret['match'] = $match;
				$records[] = $ret;

			} //parse the items in rows





		}

		$data['records'] = $records;
		$data['opts'] = "aa";

		$return['details'] = $data;
		$return['error'] = array();

		$timer->stop("Accounts Import");

		return $GLOBALS["output"]['data'] = $return;
	}


}
