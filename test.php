<?php
require_once('config.default.inc.php');
require_once('config.inc.php');

require_once('inc/functions.php');

$link = mysql_connect($cfg['DB']['host'], $cfg['DB']['username'], $cfg['DB']['password']);
mysql_select_db($cfg['DB']['database'], $link);



$sql = "SELECT (ID) AS ID FROM `global_users` WHERE email is null;";
$result = mysql_query($sql, $link) or die(mysql_error());



$r = array();
$s = array();


	while ($row = mysql_fetch_assoc($result)) {
		$uID = $row['ID'];
		$s[] = "INSERT INTO `global_users_company` (`ID` , `cID` ,`uID` ,`allow_setup` ,`ab` ,`nf` ,`ab_permissions` ,`nf_permissions` ,`ab_marketerID` ,`ab_productionID` ,`nf_author`) VALUES (NULL , '1', '$uID', '0', '0', '1', NULL , NULL , NULL , NULL , '0');";
		$r[] = $row['ID'];		
	}

echo implode("\n",$s);
exit();
test_array($s); 

