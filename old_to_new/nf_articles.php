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





/*
$articles = array(
	"str1" => "1234567890",
	"str2" => "2123456789"
);


similar_text($articles['str1'], $articles['str2'],$sim);
$articles['percent'] = 100 - $sim;
$articles['lev'] = levenshtein($articles['str1'], $articles['str2'], $sim);
test_array($articles);
*/

$link = mysql_connect($cfg['DB']['host'], $cfg['DB']['username'], $cfg['DB']['password']);
mysql_select_db($cfg['DB']['database'], $link);


$sql = "
SELECT ID, title,  	words, percent_last, percent_orig,
	(SELECT CONCAT(nf_articles_body.ID,'|||',body) FROM nf_articles_body WHERE nf_articles.ID = aID ORDER BY ID ASC LIMIT 0,1) as orig,
	(SELECT CONCAT(nf_articles_body.ID,'|||',body) FROM nf_articles_body WHERE nf_articles.ID = aID AND nf_articles_body.ID <> (SELECT ID FROM nf_articles_body WHERE nf_articles.ID = aID ORDER BY ID ASC LIMIT 0,1) ORDER BY ID DESC LIMIT 0,1)  as latest
FROM  nf_articles
ORDER BY ID DESC LIMIT 0,10";
$result = mysql_query($sql, $link) or die(mysql_error());


$articles = array();

echo '<link rel="stylesheet" type="text/css" href="/ui/_css/style.8228202248.css"/>';
echo '<style>
body {
	overflow: auto;
	padding: 20px;
}
ins, ins p {
	color: green;
	background: #dfd;
	text-decoration: none;
}
del, del p {
	color: red;
	background: #fdd;
	text-decoration: none;
}

</style>';

while ($row = mysql_fetch_assoc($result)) {
	$orig = $row['orig'];
	$latest = $row['latest'];
	$origID = "";
	$latestID = "";
	if ($orig){
		$orig = explode("|||", $orig);
		$origID = $orig[0];
		$orig = $orig[1];
	}
if ($latest) {
		$latest = explode("|||", $latest);
		$latestID = $latest[0];
		$latest = $latest[1];
}





	similar_text($orig, $latest, $sim);
	$percent = 100 - $sim;
	$row['percent_last'] = $percent;
	$row['percent_orig'] = $percent;


	$words = $latest;
	$words = $f3->scrub($words);

	$row['words'] = str_word_count($words);



	$articles[] = $row;

	$label = "";
	if ($orig){
		$label .= "<small>Percent:</small> <span class='label ' style=' margin-right: 5px;'>" . $percent . "</span>";
	} else {
		//$label .= "<span class='span1' style='padding: 4px; margin-right: 5px;'> </span>";
	}

	if ($row['words']){
		$label .= "<small>Words:</small> <span class='label label-info ' style=' margin-right: 5px;'>" . $row['words'] . "</span>";
	} else {
		//$label .= "<span class='span1' style='padding: 4px; margin-right: 5px;'> </span>";
	}




	$diff = FineDiff::getDiffOpcodes($orig, $latest, FineDiff::wordDelimiters);
	$diffHTML = FineDiff::renderDiffToHTMLFromOpcodes($orig, $diff);
	$diffHTML = htmlspecialchars_decode($diffHTML);


	echo "<h4>" . $row['title'] . "</h4>\n";
	echo "<h4>$label</h4>\n";
	echo "origID: ".$origID." | LatestID: ".$latestID;
	echo '<div style="padding-top: 10px;">';
	echo $diffHTML;
	echo '</div>';
	echo "<hr>";
	echo $latest;

	echo "<hr>";


}
/*
$articles = array(
	"str1"=>"123",
	"str2"=>"456",
);
similar_text($articles['str1'], $articles['str2'], $sim);
$articles['percent'] = 100-$sim;
test_array($articles);*/
?>
