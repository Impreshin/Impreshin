<?php
/**
 * User: William
 * Date: 2012/05/31 - 4:01 PM
 */
namespace apps\nf\controllers\admin\data;

use \timer as timer;
use \apps\nf\models as models;
use \models\user as user;


class cmstylesheet extends \apps\nf\controllers\data\data {
	function __construct() {
		parent::__construct();

	}
	function render() {

		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$pID = $user['pID'];
		
		$css = isset($_POST['style'])?$_POST['style']:"";
		$html = isset($_POST['html'])?$_POST['html']:'';

		
		echo '<style>';
		echo $css;
		echo '</style>';
		
		echo '<div id="cm-block-outer">';
		echo '<div id="cm-block">';
		echo $html;
		echo '</div>';
		echo '</div>';
		exit();
	}
	function _details(){
		$user = $this->f3->get("user");
		$userID = $user['ID'];
		$cID = $user['company']['ID'];
		$cfg = $this->f3->get("CFG");
		$ID = (isset($_REQUEST['categoryID'])) ? $_REQUEST['categoryID'] : "";

		$o = new models\categories();
		$details = $o->get($ID);

		$cmstyle =  $user['company']['nf_cm_css'];
		if ($cmstyle=='' || $cmstyle ==NULL){
			$cmstyle = $cfg['nf']['default_cm_calc_css'];
		}
		
		if ($details['ID']){
			if ($details['nf_cm_css']) $cmstyle = $details['nf_cm_css'];
		}
		
		
		
		$return['style'] = clean_style($cmstyle);



		return $GLOBALS["output"]['data'] = $return;
	}
	

}
