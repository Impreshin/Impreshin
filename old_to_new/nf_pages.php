<?php
/*
 * Date: 2013/08/21
 * Time: 12:04 PM
 */

$cfg = array();
require_once('../config.default.inc.php');
require_once('../config.inc.php');

require_once('../inc/functions.php');
require_once('../update/update.php');
include '../lib/finediff2.php';

$f3 = include_once("../lib/f3/base.php");






$link = mysql_connect($cfg['DB']['host'], $cfg['DB']['username'], $cfg['DB']['password']);
mysql_select_db($cfg['DB']['database'], $link);


$sql = "
SELECT * FROM nf_article_newsbook WHERE plannedPage is not null AND pageID is null ORDER BY ID DESC ";
$result = mysql_query($sql, $link) or die(mysql_error());


$p = array();
while ($row = mysql_fetch_assoc($result)) {
	$value = $row['plannedPage'].".0";
	$dID = $row['dID'];
	$pID = $row['pID'];

	$sql = "SELECT ID,pID,dID,`page` FROM `global_pages` WHERE `page` = '$value' AND dID = '$dID' AND pID = '$pID' LIMIT 0,1;";
	$res = mysql_query($sql, $link) or die(mysql_error());
	$lookup = mysql_fetch_assoc($res);

	$row['lookup'] = $lookup;
	$ID = $lookup['ID'];


	if (!$ID) {
		mysql_query("INSERT INTO global_pages (pID,dID,`page`) VALUES ('$pID','$dID','$value');", $link) or die(mysql_error());
		$new_res = mysql_query("SELECT ID FROM global_pages ORDER BY ID DESC LIMIT 0,1", $link) or die(mysql_error());
		$new_row = mysql_fetch_assoc($new_res);
		$ID = $new_row['ID'];
	}
	$rowID = $row['ID'];
	mysql_query("UPDATE `nf_article_newsbook` SET `pageID` = '$ID' WHERE ID =  '$rowID';", $link) or die(mysql_error());

}
echo "done";



?>
