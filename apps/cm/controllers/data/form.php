<?php

namespace apps\cm\controllers\data;

use apps\cm\models as models;
use timer as timer;


class form extends data {
	function __construct() {
		parent::__construct();


	}

	function _details($directreturn = false) {
		$timer = new timer();
		$ID = (isset($_REQUEST['ID'])) ? $_REQUEST['ID'] : "";

		$settings = models\settings::_read("form");
		$user = $this->f3->get("user");
		$type = (isset($_REQUEST['type'])) ? $_REQUEST['type'] : $settings['type'];
		if ($ID) {
			$type = substr($ID, 0, 2);
			$ID = preg_replace("/[^0-9]/", "", $ID);
		}

		$values["form"] = array(
			"type" => $type
		);

		models\settings::save($values);



		$settings['type'] = $type;

		switch ($type) {
			case "co":
				$data = $this->form_co($ID);
				break;
			case "pe":
				$data = $this->form_pe($ID);
				break;
			case "in":
				$data = $this->form_in($ID);
				break;
			case "ta":
				$data = $this->form_ta($ID);
				break;
			default:
				$data = array();

		}

		$data['linkID'] = $type . "-".$data['ID'];


		$return = array();
		$return['details'] = $data;
		$return['type'] = $type;










		$return['settings'] = $settings;
		$permissions = $user['permissions'];

		$allow = array(
			'delete' => '0'
		);
		$return['a'] = $allow;


		$timer->stop("Controller - _details", array("ID" => "", "title" => ""));
		if ($directreturn) {
			return $return;
		}

		return $GLOBALS["output"]['data'] = $return;
	}

	function form_co($ID = "") {
		$return = array();

		$d = new models\companies();
		$return = $d->get($ID);



		return $return;
	}

	function form_pe($ID = "") {
		$return = array();

		$d = new models\contacts();
		$return = $d->get($ID);



		return $return;
	}


	function form_in($ID = "") {
		$return = array();

		$d = new models\contacts();
		$return = $d->get($ID);



		return $return;
	}

	function form_ta($ID = "") {
		$return = array();

		$d = new models\contacts();
		$return = $d->get($ID);



		return $return;
	}

}
