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
		$pID = (isset($_REQUEST['pID'])) ? $_REQUEST['pID'] : "";
		$dID = (isset($_REQUEST['dID'])) ? $_REQUEST['dID'] : "";
		$newsbook = (isset($_REQUEST['newsbook'])) ? true : false;
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
				$orig = htmlspecialchars_decode($orig);
				$latest = htmlspecialchars_decode($latest);
				$diff = \FineDiff::getDiffOpcodes($orig, $latest, \FineDiff::characterDelimiters);
				$diffHTML = \FineDiff::renderDiffToHTMLFromOpcodes($orig, $diff);
				$diffHTML = htmlspecialchars_decode($diffHTML);
				$compare['body'] = $diffHTML;
			}



		}

		
		$newsbooks = models\articles::getNewsbooks($return['ID'],"publish_date DESC");

		$n = array();
		$media_show = array();
		
			
		
		foreach ($newsbooks as $item){
			$media= models\files::getAll("nf_article_newsbook_photos.nID='".$item['ID']."'");
			
			$media = models\files::display($media);
			if ($newsbook){
				if ($item['pID']==$pID&&$item['dID']==$dID){
					$media_show = $media;
				}
			}
			$item['media']= $media;
			$n[] = $item;
		}

		if ($newsbook){
			$return['media'] = $media_show;
		}
		$newsbooks = $n;
		
		//test_array($newsbooks);
		$return['used'] = $newsbooks;

		
		
//		test_array($return['newsbooks']); 

		$return['historyShow'] = $compare;
		$return['history'] = $history;
		$return['logs'] = models\articles::getLogs($return['ID']);


		$return['a'] = $allow;

		return $GLOBALS["output"]['data'] = $return;
	}
	function comments() {
		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";

		$data = models\comments::getAll("aID='" . $ID . "'", "ID DESC");
		
		$return['count'] = count($data);
		$return['comments'] = models\comments::display($data);

		

		return $GLOBALS["output"]['data'] = $return;
	}
	function newsbooks(){
		$user = $this->f3->get("user");
		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";
		$aID = (isset($_REQUEST['aID'])) ? $_REQUEST['aID'] : "";
		
		
		$article = new models\articles();
		$article = $article->get($aID);
		
		
		$record = new models\newsbooks();
		$record = $record->get($ID);
		$in_newsbook = array();
		if ($record['ID']){
			$pID = $record['pID'];
			$curFiles = models\files::getAll("nf_article_newsbook_photos.nID='".$record['ID']."'");
			foreach ($curFiles as $item){
				$in_newsbook[] = $item['ID'];
			}
		} else {
			$pID = $user['publication']['ID'];
		}
		

		$pID = (isset($_REQUEST['pID'])) ? $_REQUEST['pID'] : $pID;
		

		$currentDate = \models\dates::getCurrent($pID);
		
		$dID = (isset($_REQUEST['dID'])) ? $_REQUEST['dID'] : $currentDate['ID'];
		
		$newsbooks = \models\publications::getAll("cID='".$article['cID']."'");

		//test_array($newsbooks); 
		$n = array();
		foreach ($newsbooks as $item){
			
			$n[] = array(
				"ID"=>$item['ID'],
				"label"=>$item['publication'],
				"selected"=>($pID==$item['ID'])?1:0,
			);
		}
		$newsbooks = $n;
		
		

		$dates = \models\dates::getAll("pID='".$pID."' AND publish_date >= '" . $currentDate['publish_date'] . "'", "publish_date ASC", "");
		$n = array();
		foreach ($dates as $item){

			$n[] = array(
				"ID"=>$item['ID'],
				"label"=>$item['publish_date_display'],
				"selected"=>($dID==$item['ID'])?1:0,
			);
		}
		$dates = $n;
		
		$media = $article['media'];

		
		
		$n = array();
		foreach ($media as $item){
			$item['checked']=(in_array($item['ID'],$in_newsbook))?1:0;

			
			$n[] = $item;
		}
		$media = $n;
		
		$return['details'] = $record;
		$return['publications'] = $newsbooks;
		$return['dates'] = $dates;
		$return['media'] = $media;
		
		//test_array($return); 
		
		
		return $GLOBALS["output"]['data'] = $return;
	}





}
