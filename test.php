<?php
require_once('config.default.inc.php');
require_once('config.inc.php');

require_once('inc/functions.php');

$t = '<p style="margin-left:72.0pt">s<em>po</em>t <strong>1</strong></p><p>spot 2</p>';



$t = preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i",'<$1$2>', $t);


echo $t;