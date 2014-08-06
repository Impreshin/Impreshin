<?php
namespace apps\ab\controllers\data;
use \timer as timer;
use \apps\ab\models as models;
use \models\user as user;

class data {

	protected $f3;
	function __construct() {
		$this->f3 = \base::instance();
		$user = $this->f3->get("user");
		$userID = $user['ID'];

		$this->f3->set("json",true);
		$GLOBALS["output"]['notifications'] = \apps\ab\models\notifications::show();
	}

	function __destruct() {


	}


	function details(){

		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : exit(json_encode(array("error"=> $this->f3->get("system")->error("B01"))));

		$user = $this->f3->get("user");

		$cfg = $this->f3->get("CFG");
		$record = new models\bookings();
		$return = $record->get($ID);





		$allow = array(
			"repeat"=>"0",
			"edit"=>"0",
			"print"=>"1",
			"material_pane"=>"0",
			"checked"=>"0",
			"material"=>"0",
			"invoice"=>"0"

		);

		if (!$return['deleted']){
			$allow['invoice'] = '1';
			if (isset($return['accountBlocked']) && (!$return['accountBlocked'] && $return['accNum'])){
				$allow['repeat'] = '1';
			}



			if (isset($return['state']) && $return['state']=='Current'){
				$allow['checked'] = '1';

			}
			if (isset($return['state']) && ($return['state'] == 'Current' || $return['state'] == 'Future')){
				$allow['material'] = '1';
				$allow['edit'] = '1';
			}
			if ($cfg['upload']['material']) {
				if ($return['material_file_filename']&& $return['material_status']){
					$allow['material_pane'] = '1';
				}

			}
		}

		if ($return['pageID']) {
			$allow['edit'] = "0";
			$allow['checked'] = "0";
		}

		if ($return['checked']){
			$allow['edit'] = "0";
		}




		$permissions = $user['permissions'];
		if ($permissions['details']['actions']['check']=='0') $allow['checked'] = 0;
		if ($permissions['details']['actions']['material']=='0') $allow['material'] = 0;
		if ($permissions['details']['actions']['repeat']=='0') $allow['repeat'] = 0;
		if ($permissions['form']['edit']=='0') $allow['edit'] = 0;
		if ($permissions['details']['actions']['invoice']=='0') $allow['invoice'] = 0;
		//if ($permissions['actions']['delete']=='0') $allow['delete'] = '0';

		if ($permissions['form']['edit_master']) {
			$allow['edit'] = "1";
			$allow['checked'] = "1";
		}

		$return['a'] = $allow;


		//test_array($return); 
		
		$return['page_details'] = $this->page_info($return['page'],$return['pID'],$return['dID']);

		return $GLOBALS["output"]['data'] = $return;
	}

	function page_info($page_nr,$pID,$dID){
		

		$user = $this->f3->get("user");
		$userID = $user['ID'];

		

		$page = models\pages::getAll("page='$page_nr' AND global_pages.dID = '$dID' AND global_pages.pID='$pID'");

		if (count($page)) {
			$page = $page[0];

		} else {
			$page = models\pages::dbStructure();
			$page['page'] = $page_nr;
		}
		$pageID = $page['ID'];
		


		$bookingsRaw = models\bookings::getAll("(ab_bookings.pID = '$pID' AND ab_bookings.dID='$dID') AND checked = '1' AND ab_bookings.deleted is null AND typeID='1' AND pageID='$pageID'", "client ASC");
		$bookings = array();
		$cm = 0;
		$records = 0;
		foreach ($bookingsRaw as $booking) {
			//test_array($booking); 
			$a = array();
			$a['ID'] = $booking['ID'];
			$a['client'] = $booking['client'];
			$a['colour'] = $booking['colour'];
			$a['colourLabel'] = $booking['colourLabel'];
			$a['col'] = $booking['col'] + 0;
			$a['cm'] = $booking['cm'] + 0;
			$a['totalspace'] = $booking['totalspace'] + 0;
			$a['pageID'] = $booking['pageID'];
			$a['page'] = $booking['page'];
			$a['material'] = $booking['material'];
			$a['material_approved'] = $booking['material_approved'];
			$a['material_status'] = $booking['material_status'];
			//$a['material_file_filename'] = $booking['material_file_filename'];
			$a['material_file_store'] = $booking['material_file_store'];
			$a['x_offset'] = $booking['x_offset']?$booking['x_offset']+0:"";
			$a['y_offset'] = $booking['y_offset']?$booking['y_offset']+0:"";

			$bookings[] = $a;
			if ($a['cm']) $cm = $cm + $a['totalspace'] + 0;
			$records++;
		}

		$page['records']= $bookings;


		$pageSize = $user['publication']['cmav'] * $user['publication']['columnsav'];
		$totalAVspace = $pageSize;
		$loading = ($cm) ? ($cm / $totalAVspace) * 100 : 0;
		$loading = number_format($loading, 2);



		$page['stats'] = array(
			"cm"=>$cm,
			"records" => $records,
			"loading" => $loading
		);
		return $GLOBALS["output"]['data'] = $page;
	}
	
	




}
