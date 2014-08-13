<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\pf\controllers\save;

use \timer as timer;
use \apps\pf\models as models;
use \models\user as user;


class front extends save {
	function __construct() {
		parent::__construct();

	}


	function _upload_page(){
		$user = $this->f3->get("user");
		$ID = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : "";
		$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : "";
		$pID = isset($_REQUEST['pID']) ? $_REQUEST['pID'] : "";
		$dID = isset($_REQUEST['dID']) ? $_REQUEST['dID'] : "";
		$filename = isset($_REQUEST['filename']) ? $_REQUEST['filename'] : "";

		$pID = $user['pID'];


		if ($page && ($page != "remove") ){
			$a = new \DB\SQL\Mapper($this->f3->get("DB"),"global_pages");
			$a->load("pID = '$pID' AND dID = '$dID' AND page = '$page'");
			if ($a->dry()) {
				$a->pID = $pID;
				$a->dID = $dID;
				$a->page = $page;
				$a->pdf = $filename;
				$a->pdf_uID = $user['ID'];
				$a->pdf_datein = date("Y-m-d H:i:s");

				$a->save();

				$pageID = $a->_id;
			} else {
				$a->pdf = $filename;
				$a->pdf_uID = $user['ID'];
				$a->pdf_datein = date("Y-m-d H:i:s");
				$a->save();
				$pageID = $a->ID;
			}
		} else {
			$pageID = NULL;
		}



		return $GLOBALS["output"]['data'] = $pageID;

	}

}
