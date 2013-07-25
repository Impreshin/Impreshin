<?php
/*
 * Date: 2011/11/16
 * Time: 11:29 AM
 */
namespace models;



use \timer as timer;

class _system {
	public $ID;
	private $dbStructure;

	function __construct() {
		$classname = get_class($this);
		$this->dbStructure = $classname::dbStructure();

	}

	public static function payment_methods_getAll() {
		$timer = new timer();
		$f3 = \Base::instance();
		$result = $f3->get("DB")->exec("SELECT * FROM system_payment_methods ORDER BY orderby");

		//test_array($result);
		$timer->stop(array("Models" => array("Class" => __CLASS__, "Method" => __FUNCTION__)), func_get_args());
		return $result;
	}

}
