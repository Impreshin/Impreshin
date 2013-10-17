<?php
require_once('config.default.inc.php');
require_once('config.inc.php');

require_once('inc/functions.php');

$link = mysql_connect($cfg['DB']['host'], $cfg['DB']['username'], $cfg['DB']['password']);
mysql_select_db($cfg['DB']['database'], $link);



$sql = "SELECT GROUP_CONCAT(ID) AS ID FROM `global_users` WHERE email is null;";
$result = mysql_query($sql, $link) or die(mysql_error());



$r = array();
$s = array();


	while ($row = mysql_fetch_assoc($result)) {
		$r[] = $row['ID'];		
	}

test_array($r); 