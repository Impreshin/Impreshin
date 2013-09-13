<?php
namespace apps\nf\controllers\data;
use \timer as timer;
use \apps\nf\models as models;
use \models\user as user;

class data {

	protected $f3;
	function __construct() {
		$this->f3 = \base::instance();

		$this->f3->set("json",true);
		$GLOBALS["output"]['notifications'] = \apps\nf\models\notifications::show();
	}

	function __destruct() {


	}

	function details() {
		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";
		$historyID = (isset($_REQUEST['historyID'])) ? $_REQUEST['historyID'] : "";
		$user = $this->f3->get("user");


		$record = new models\articles();
		$return = $record->get($ID);
		$allow = array("print" => "1",);

		$permissions = $user['permissions'];


		
		
		
		$history = array();

		$historyData = models\articles::getEdits($ID,"datein ASC");

		$compare = array();
		$previous = array();
		$prev = array();
		$i=0;
		foreach ($historyData as $item){

			if ($historyID== $item['ID']) {
				$compare = $item;
				$previous = $prev;
			}

			$prev = $item;
			unset($item['body']);
			$history[$item['datein']] = $item;
		}


		rsort($history);

		//test_array(array("data" => $history, "c" => $compare, "t" => $previous));


		if (isset($compare['body'])&&isset($previous['body'])){

			$orig = $previous['body'];
			$latest = $compare['body'];

			//test_array(array("o"=>$orig,"l"=>$latest));

			if ($orig!= $latest){
				$diff = \FineDiff::getDiffOpcodes($orig, $latest, \FineDiff::characterDelimiters);
				$diffHTML = \FineDiff::renderDiffToHTMLFromOpcodes($orig, $diff);
				$diffHTML = htmlspecialchars_decode($diffHTML);
				$compare['body'] = $diffHTML;
			}



		}



		

		$return['historyShow'] = $compare;
		$return['history'] = $history;
		$return['logs'] = models\articles::getLogs($return['ID']);


		$return['a'] = $allow;

		return $GLOBALS["output"]['data'] = $return;
	}





}
