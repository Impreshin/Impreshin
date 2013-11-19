<?php
require_once('config.default.inc.php');
require_once('config.inc.php');

require_once('inc/functions.php');
require_once('lib/finediff_orig.php');


$old = "<p>Daisy maak haar o&euml; oop en soek <strong>dadelik </strong>na &lsquo;n Panado. Sy weet nog nie wat haar naam is of watter jaar dit is nie, maar sy is alreeds kwaad. Vanoggend is daar g&rsquo;n teken van haar spirituele beginsel om elke dag met groot dankbaarheid te begin nie.</p>

<p>Die Bosbewoner sit op &lsquo;n leunstoel langs Daisy en drink sy koffie. Hy loer bekommerd na haar en verkies om die woorde waarmee sy wakker word te ignoreer.</p>";

$new = "<p>Daisy maak haar o&euml; oop en soek <strong>dadelik </strong>na &lsquo;n Panado. Sy weet nog nie wat haar naam is of watter jaar dit is nie, maar sy is alreeds kwaad.</p>

<p>Vanoggend is daar g&rsquo;n teken van haar spirituele beginsel om elke dag met groot dankbaarheid te begin nie.Die Bosbewoner sit op &lsquo;n leunstoel langs Daisy en drink sy koffie. Hy loer bekommerd na haar en verkies om die woorde waarmee sy wakker word te ignoreer.</p>";


$old = htmlspecialchars_decode($old);
$new = htmlspecialchars_decode($new);

$myStack = array(
	\FineDiff::wordDelimiters,
	\FineDiff::characterDelimiters,
);


$diff = \FineDiff::getDiffOpcodes($old, $new, $myStack);
$diffHTML = \FineDiff::renderDiffToHTMLFromOpcodes($old, $diff);

echo '<style>ins {background: none repeat scroll 0 0 #DDFFDD; color: #008000; text-decoration: none;};</style>';
echo '<style>del {background: none repeat scroll 0 0 #FFDDDD; color: #FF0000; text-decoration: none;};</style>';
echo $diffHTML;