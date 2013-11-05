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
	

}
