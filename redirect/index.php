<?php
require_once('../config.default.inc.php');
require_once('../config.inc.php');

$redirect = $cfg['HOST_DOMAIN_COMPLETE']."/".$_SERVER['REQUEST_URI'];
header("HTTP/1.1 301 Moved Permanently");
header("Location: $redirect");

?>
