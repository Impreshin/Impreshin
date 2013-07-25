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
		if (!$userID) exit(json_encode(array("error" => $this->f3->get("system")->error("U01"))));
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

		return $GLOBALS["output"]['data'] = $return;
	}






}
