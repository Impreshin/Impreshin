<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\nf\controllers\admin\save;


use \timer as timer;
use \apps\nf\models as models;
use \models\user as user;


class cmstylesheet extends \apps\nf\controllers\save\save {
	function __construct() {
		parent::__construct();


	}

	function _save() {
		$user = $this->f3->get("user");
		$pID = $user['publication']['ID'];
		$cID = $user['company']['ID'];


		$ID = isset($_REQUEST['categoryID']) ? $_REQUEST['categoryID'] : "";


		

		$values = array(
			"nf_cm_css"=>clean_style((isset($_POST['cm-block-form'])?$_POST['cm-block-form']:NULL),true)
		);
		
		
		if ($ID!=""){
			models\categories::save($ID,$values);
		} else {
			\models\company::save($cID,$values);
		}




		


		return $GLOBALS["output"]['data'] = $values['nf_cm_css'];

	}



	

	
}
