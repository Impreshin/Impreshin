<?php

namespace apps\nf\share;
class share {
	function __construct() {
		$this->f3 = \base::instance();
		$this->user = $this->f3->get("user");

	}
}
