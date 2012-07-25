<?php
/*
 * Date: 2012/07/25
 * Time: 1:49 PM
 */
require_once('../config.inc.php');
require_once('../inc/functions.php');
require_once('update.php');



echo "Updating...<hr>";
echo "<h3>Files</h3>";
echo "<pre>" . update::code() . "</pre>";
echo "<h3>Database</h3>";
echo "<pre> " . update::db($cfg) . "</pre>";
echo "<hr>Done!!";
exit();

?>
