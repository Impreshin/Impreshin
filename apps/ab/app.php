<?php
/**
 * User: William
 * Date: 2013/07/17 - 12:34 PM
 */
namespace apps\ab;

class app extends \apps\app{
	function __construct() {
		parent::__construct();
		$this->namespace = __NAMESPACE__;
	}
	function app(){
		$user = $this->user;

		$this->user = parent::user();
		$this->f3->set("user", $this->user);
		//test_array($settings);



	}
}
