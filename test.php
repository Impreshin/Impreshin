<?php
$f3 = require('lib/f3/base.php');
$f3->route('GET /', function ($f3) {
		phpinfo();
	}
);
$f3->run();