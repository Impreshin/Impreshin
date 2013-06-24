<?php
$f3 = require('lib/f3/base.php');
$f3->route('GET /test.php', function ($f3) {

		$d = file_get_contents("https://github.com/Impreshin/Impreshin/graphs/commit-activity-data");
		header("Content-Type: application/json");
		echo $d;

		exit();

	}
);
$f3->run();