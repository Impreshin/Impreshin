<?php

namespace apps\nf\models;



use \timer as timer;

class files {
	private $classname;

	function __construct() {

		$classname = get_class($this);
		$this->dbStructure = $classname::dbStructure();

	}

	function get($ID) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		$userID = $user['ID'];


		$result = $f3->get("DB")->exec("
			SELECT *
			FROM nf_files
			WHERE ID = '$ID';

		");


		if (count($result)) {
			$return = $result[0];
		} else {
			$return = $this->dbStructure;
		}
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function getAll($where = "", $orderby = "", $pID="") {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");
		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}

		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}


		$result = $f3->get("DB")->exec("
			SELECT *
			FROM nf_files
			$where
			$orderby
		");


		$return = $result;
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $return;
	}

	public static function display($data, $highlight = "") {
		$return = array();

		$settings = \apps\nf\settings::_available();
		
		$filetypes = $settings['allowedFileTypes'];


		foreach ($data as $file) {

			$icon = "file";

			$file_ext = strtolower($file['filename']);
			$file_ext = explode(".", $file_ext);
			$file_ext = $file_ext[count($file_ext) - 1];

			$filetype = "0";
			foreach ($filetypes as $k => $v) {
				foreach ($v as $key => $item) {
					if (in_array($file_ext, $item)) {
						$icon = $key;
						$filetype = $k;
					}
				}
			}
			$file['fileType'] = $filetype;
			$file['icon'] = $icon;



			$return[] = $file;
		}



		return $return;
	}

	public static function save($ID, $values) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");


		$old = array();


		$a = new \DB\SQL\Mapper($f3->get("DB"),"nf_files");
		$a->load("ID='$ID'");

		foreach ($values as $key => $value) {
			$old[$key] = isset($a->$key) ? $a->$key : "";
			if (isset($a->$key)) {

				$a->$key = $value;
			}

		}

		if (!$a->dry()) {
			$label = "Record Edited ($a->filename_orig)";
		} else {
			$label = "Record Added (" . $values['filename_orig'] . ')';
		}

		$a->save();


		$ID = $a->ID;

		

		\models\logging::_log("nf_files", $label, $values, $old);


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $ID;

	}

	public static function _delete($ID) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");


		$a = new \DB\SQL\Mapper($f3->get("DB"),"nf_files");
		$a->load("ID='$ID'");

		$a->erase();

		$a->save();


		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return "done";

	}


	private static function dbStructure() {
		$f3 = \Base::instance();
		$table = $f3->get("DB")->exec("EXPLAIN nf_files;");
		$result = array();
		foreach ($table as $key => $value) {
			$result[$value["Field"]] = "";
		}

		return $result;
	}
}