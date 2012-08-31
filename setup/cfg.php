<?php
if (php_sapi_name() == 'cli'){
	$cfg = array();
	require_once('/media/data/web/config.default.inc.php');
	require_once('/media/data/web/config.inc.php');

	foreach ($cfg['DB'] as $key=> $val) {
		echo "db_$key:$val\n";
	}
	foreach ($cfg['git'] as $key=> $val) {
		echo "git_$key:$val\n";
	}
}



?>
