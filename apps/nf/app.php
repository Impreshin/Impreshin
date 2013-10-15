<?php
/**
 * User: William
 * Date: 2013/07/17 - 12:34 PM
 */
namespace apps\nf;

class app extends \apps\app{
	function __construct() {
		parent::__construct();
		$this->namespace = __NAMESPACE__;
	}
	function app(){
		$user =parent::user();

		$permissions = $user['permissions'];
		$permissions['records']['_nav'] = '0';
		foreach ($permissions['records'] as $p) {
			if ($p['page']) {
				$permissions['records']['_nav'] = '1';
			}
		}

		$user['permissions'] = $permissions;
		//test_array($user); 
		$this->user = $user;
		
		
		$this->f3->set("user", $this->user);
		//test_array($settings);



	}
	
}
