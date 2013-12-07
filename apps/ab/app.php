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

		
		$user = parent::user();
		if (isset($this->user['extra']['ab_marketerID'])&&$this->user['extra']['ab_marketerID']){
			$marketerO = new \apps\ab\models\marketers();
			$marketer = $marketerO->get($user['extra']['ab_marketerID']);
			$user['marketer'] = $marketer;
		}
		$permissions = $user['permissions'];
		if ($user['su'] == '1') {
			$permissions['view']['only_my_records'] = '0';
		}


		$permissions['records']['_nav'] = '0';
		foreach ($permissions['records'] as $p) {
			if (isset($p['page'])&&$p['page']) {
				$permissions['records']['_nav'] = '1';
			}
		}

		if (isset($user['marketer']['ID']) && $user['marketer']['ID']) {
			$permissions['reports']['_nav'] = '1';
			$permissions['reports']['marketer']['_nav'] = '1';
			foreach ($permissions['reports']['marketer'] as $k => $p) {
				$permissions['reports']['marketer'][$k]['spage'] = '1';

			}

		}
		$user['permissions'] = $permissions;
		$this->user = $user;

		if (isset($_GET['debug']) && $_GET['debug']=="user"){
			test_array($user);
		}
		$this->f3->set("user", $user);
		return $user;



	}
}
