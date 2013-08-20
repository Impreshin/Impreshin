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






}
