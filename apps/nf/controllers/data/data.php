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
		$history_type = (isset($_REQUEST['history'])) ? $_REQUEST['history'] : "";
		$user = $this->f3->get("user");


		$record = new models\articles();
		$return = $record->get($ID);
		$allow = array(
			"newsbook"=>"0",
			"locked"=>"0",
			"reject"=>"0",
			"delete"=>"0",
			"archive"=>"0",
			"edit"=>"0",
			"print" => "1",
			"stageNext"=>"0",
			"stagePrev"=>"0",
			"stage_jump_list"=>"0"
		);


		$permissions = $user['permissions'];
		$stage_permissions = isset($permissions['stages'][$return['stageID']])?$permissions['stages'][$return['stageID']]:array("label"=>"-none-",
			"edit"=>"0",
			"to"=>"0",
			"reject"=>"0",
			"delete"=>"0",
			"newsbook"=>"0");
		
		if ($stage_permissions['edit']=='1'){
			if ($return['locked_uID']){
				if ($return['locked_uID']==$user['ID']){
					$allow['edit'] = '1';
				}
			} else {
				$allow['edit'] = '1';
			}
		}
		if ($stage_permissions['delete']=='1'){
			$allow['delete']='1';
		}
		if ($stage_permissions['newsbook']=='1'){
			$allow['newsbook']='1';
		}
		if ($stage_permissions['reject']=='1'){
			$allow['reject']='1';
		}
		if ($permissions['details']['overwrite_locked']=="1" || ($return['locked_uID']==$user['ID'] && $return['stageID']!='1')){
			$allow['locked']='1';
		}
		if ($permissions['details']['archive']=="1"){
			$allow['archive']='1';
		}
		if ($permissions['details']['stage_jump_list']=="1"){
			$allow['stage_jump_list']='1';
		}
		if ($permissions['form']['edit_master']=="1"){
			$allow['edit']='1';
		}
		
		

		$return['stageNext'] = models\stages::getNext($return['stageID']);
		$return['stagePrev'] = models\stages::getPrev($return['stageID']);

		if (isset($return['stageNext']['ID']) && isset($permissions['stages'][$return['stageNext']['ID']]['to'])){
			if ($permissions['stages'][$return['stageNext']['ID']]['to']=='1'){
				$allow['stageNext']='1';
			}
		}

		if (isset($return['stagePrev']['ID']) && isset($permissions['stages'][$return['stagePrev']['ID']]['to'])){
			if ($permissions['stages'][$return['stagePrev']['ID']]['to']=='1' && $allow['edit']=='1'){
				$allow['stagePrev']='1';
			}
		}
		
	
		

		$stages = $permissions['stages'];
		$n = array();
		foreach ($stages as $key=>$item){
			if ($item['to']=='1'){
				$n[] = array(
					"ID"=>$key,
					"label"=>$item['label'],
				);
			}
			
		}
		$stages = $n;
	
		$return['stages'] = $stages;
		

		/*
		if ((isset($return['authorID']) && $return['authorID']==$user['ID']) || (!$return['locked_uID'] && $permissions['form']['edit']=='1')){
			// stage stuff
			$allow['edit'] = '1';
		}*/
		
		
		
		$history = array();

		$historyData = models\articles::getEdits($ID,"datein ASC");

		$compare = array();
		$previous = array();
		$prev = array();
		$i=0;
		foreach ($historyData as $item){

			if ($historyID== $item['ID'] &&$history_type=='body' ) {
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
			
			$item['media']= $media;
			$n[] = $item;
		}

		
		$newsbooks = $n;
		
		//test_array($newsbooks);
		$return['used'] = $newsbooks;
		$history_data = "";
		
		SWITCH($history_type){
			CASE 'body':
				$history_data = $compare;
				break;
			CASE 'media':

				$fileO = new models\files();
				$file =  $fileO->get($historyID);
				$file = models\files::display(array($file));
				$history_data = $file[0];
				$history_data['history'] =	models\files::getHistory($historyID,"ID DESC");
				break;
		}
		
		
//		test_array($return['newsbooks']); 

		$return['historyShow'] = array(
			"ID"=>$historyID,
			"type"=>$history_type,
			"data"=>$history_data
		);
		
		$return['history'] = $history;
		$return['logs'] = models\articles::getLogs($return['ID']);


		$return['a'] = $allow;

		return $GLOBALS["output"]['data'] = $return;
	}
	function details_newsbook() {
		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";
		$historyID = (isset($_REQUEST['historyID'])) ? $_REQUEST['historyID'] : "";
		$pID = (isset($_REQUEST['pID'])) ? $_REQUEST['pID'] : "";
		$dID = (isset($_REQUEST['dID'])) ? $_REQUEST['dID'] : "";
		$newsbook = (isset($_REQUEST['newsbook'])) ? true : false;
		$user = $this->f3->get("user");


		$record = new models\articles();
		$return = $record->get($ID,array("pID"=>$pID,"dID"=>$dID));
		$allow = array("print" => "1",);

		$permissions = $user['permissions'];


		
		
		
		$history = array();

	



		
		$newsbook = models\newsbooks::getAll("aID='".$return['ID']."' AND pID = '$pID' AND dID = '$dID'","ID DESC");

		if (count($newsbook)) $newsbook = $newsbook[0];
		$media = array();
		if (isset($newsbook['ID'])){
			$media= models\files::getAll("nf_article_newsbook_photos.nID='".$newsbook['ID']."'");

			$media = models\files::display($media);
		}
	

		$return['media'] = $media;


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

function file_details(){
		$user = $this->f3->get("user");
		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";

	$cfg = $this->f3->get("CFG");


	$fileO = new models\files();
	$return = $fileO->get($ID);

	$file = ($cfg['upload']['folder'] . "nf/") .$return['folder'] . "/" . $return['filename'] ;

	
	$path = $this->f3->fixslashes($file);
	$path = str_replace("//","/",$path);

	$exif = array(
		"width"=>"",
		"height"=>"",
		"size"=>"",
		"dpi"=>"",
		"date"=>"",
		"date_orig"=>"",
		"description"=>"",
		"comment"=>"",
		"software"=>"",
		"copyright"=>"",
		"camera"=>array(
			"make"=>"",
			"model"=>"",
			"shutter"=>"",
			"aperture"=>"",
			"focal"=>"",
		)
		
	);
	
	
	if (is_file($path)){
		$exif_raw = @exif_read_data($path, 0, true);
		
		if (isset($_GET['raw'])){
			test_array($exif_raw);
		}
		
		if ($exif_raw){
			$a = fopen($path,'r');
			$string = fread($a,20);
			fclose($a);

			$data = bin2hex(substr($string,14,4));
			$x = hexdec(substr($data,0,4));
			$y = hexdec(substr($data,0,4));

			if ($x == $y){
				$dpi = $x."DPI";
			} else {
				$dpi = "X:".$x."DPI | Y:".$y."DPI";
			}
			$exif['dpi'] = $dpi;
		
		
			if (isset($exif_raw['COMPUTED']['Width'])) $exif['width'] = $exif_raw['COMPUTED']['Width'];
			if (isset($exif_raw['COMPUTED']['Height'])) $exif['height'] = $exif_raw['COMPUTED']['Height'];
			if (isset($exif_raw['COMPUTED']['UserComment'])) $exif['comment'] = $exif_raw['COMPUTED']['UserComment'];
			if (isset($exif_raw['COMPUTED']['Copyright'])) $exif['copyright'] = $exif_raw['COMPUTED']['Copyright'];
			
			
			if (isset($exif_raw['FILE']['FileSize'])) $exif['size'] = file_size($exif_raw['FILE']['FileSize']);
			
			
			if (isset($exif_raw['IFD0']['ImageDescription'])) $exif['description'] = $exif_raw['IFD0']['ImageDescription'];
			if (isset($exif_raw['IFD0']['DateTime'])) $exif['date'] = $exif_raw['IFD0']['DateTime'];
			if (isset($exif_raw['IFD0']['Software'])) $exif['software'] = $exif_raw['IFD0']['Software'];
			
			if (isset($exif_raw['IFD0']['Make'])) $exif['camera']['make'] = $exif_raw['IFD0']['Make'];
			if (isset($exif_raw['IFD0']['Model'])) $exif['camera']['model'] = $exif_raw['IFD0']['Model'];
			if (isset($exif_raw['EXIF']['ShutterSpeedValue'])) $exif['camera']['shutter'] = $exif_raw['EXIF']['ShutterSpeedValue'];
			if (isset($exif_raw['EXIF']['ApertureValue'])) $exif['camera']['aperture'] = $exif_raw['EXIF']['ApertureValue'];
			if (isset($exif_raw['EXIF']['FocalLength'])) $exif['camera']['focal'] = $exif_raw['EXIF']['FocalLength'];
			
			
			
			if (isset($exif_raw['EXIF']['DateTimeOriginal'])) $exif['date_orig'] = $exif_raw['EXIF']['DateTimeOriginal'];
			
			$calc = array("shutter","aperture", "focal");
			foreach ($calc as $item){
				if (strpos($exif['camera'][$item],"/")){
					$v = explode("/",$exif['camera'][$item]);
					$v = $v[0] / $v[1];
					$exif['camera'][$item] = $v;
				}
			}
			$return['exif'] = $exif;
		} 
		
	}
	
	
		
		
		
		
		return $GLOBALS["output"]['data'] = $return;
	}





}
