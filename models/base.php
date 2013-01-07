<?php
/**
 * User: William
 * Date: 2013/01/07 - 3:47 PM
 */
namespace models;
class base {
	protected $f3;

	function __construct() {
		$this->f3 = \Base::instance();
	}
}