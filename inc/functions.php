<?php


function isLocal() {
	if (file_exists("D:/web/local.txt")||file_exists("C:/web/local.txt")) {
		return true;
	} else return false;
}

function is_bot() {
	$botlist = array(
		"Teoma",
		"bingbot",
		"alexa",
		"froogle",
		"Gigabot",
		"inktomi",
		"looksmart",
		"URL_Spider_SQL",
		"Firefly",
		"NationalDirectory",
		"Ask Jeeves",
		"TECNOSEEK",
		"InfoSeek",
		"WebFindBot",
		"girafabot",
		"crawler",
		"www.galaxy.com",
		"Googlebot",
		"Googlebot",
		"Scooter",
		"Slurp",
		"msnbot",
		"appie",
		"FAST",
		"WebBug",
		"Spade",
		"ZyBorg",
		"rabaz",
		"Baiduspider",
		"Feedfetcher-Google",
		"TechnoratiSnoop",
		"Rankivabot",
		"Mediapartners-Google",
		"Sogou web spider",
		"WebAlta Crawler",
		"TweetmemeBot",
		"Butterfly",
		"Twitturls",
		"Me.dium",
		"Twiceler"
	);

	foreach ($botlist as $bot) {
		if (strpos($_SERVER['HTTP_USER_AGENT'], $bot) !== false) return true; // Is a bot
	}

	return false; // Not a bot
}

function sanitize_output($buffer) {


	return $buffer;
}
function safe($data){
	$data = str_replace("\'","\\'",$data);
	return $data;
}

function rev_nl2br($string, $line_break = PHP_EOL) {
/*	$string = preg_replace('#<\/p>#i', "", $string);
	$string = preg_replace('#<p>#i', "", $string);

*/
	return $string;


}
function is_ajax() {
	return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
}


function siteURL() {
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	$domainName = $_SERVER['HTTP_HOST'] ;
	return $protocol . $domainName;
}
function file_size($size) {
	$unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
	return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
}

function timesince($tsmp) {
	if (!$tsmp) return "";
	$diffu = array(
		'seconds'=> 2,
		'minutes'=> 120,
		'hours'  => 7200,
		'days'   => 172800,
		'months' => 5259487,
		'years'  => 63113851
	);
	$diff = time() - strtotime($tsmp);
	$dt = '0 seconds ago';
	foreach ($diffu as $u => $n) {
		if ($diff > $n) {
			$dt = floor($diff / (.5 * $n)) . ' ' . $u . ' ago';
		}
	}
	return $dt;
}
function curl_get_contents($url) {
	$ch = curl_init();
	$timeout = 5; // set to zero for no timeout
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$file_contents = curl_exec($ch);
	curl_close($ch);

	return $file_contents;
}
function currency($number){

	$number = $GLOBALS['cfg']['currency']['sign'] . number_format($number, 2, '.', $GLOBALS['cfg']['currency']['separator']);
	return str_replace(" ", "&nbsp;", $number);

}

function test_array($array,$splitter=''){

	if (!is_array($array) && $splitter){
		$array = explode($splitter,$array);
	} 
	
	if (!is_array($array)){
		echo ($array);
		exit();
	}

	header("Content-Type: application/json");
	echo json_encode($array);
	exit();
}

function bt_loop($trace) {
	if (isset($trace['object'])) unset($trace['object']);
	if (isset($trace['type'])) unset($trace['type']);


	$args = array();
	foreach ($trace['args'] as $arg) {
		if (is_array($arg)) {
			if (count($arg)<5){
				$args[]= $arg;
			} else {
				$args[] = "Array " . count($arg);
			}

		} else {
			$args[] = $arg;
		}

	}
	$trace['args'] = $args;

	return $trace;
}

function sortBy($key="order") {
	return function ($a, $b) use ($key) {
		return strnatcmp($a[$key], $b[$key]);
	};


}
function form_display(&$value) {
	if ($value){
		iconv('', 'UTF-8//IGNORE', $value);
		$value = htmlentities($value);
	}
	
}
function form_write(&$value) {
	if ($value){
		$value = html_entity_decode($value);
	}
	
}

function clean_style($cmstyle,$clearspaces=false){
	if ($cmstyle){
		$cmstyle = trim(preg_replace('/\s+/', ' ', $cmstyle));
		$cmstyle = str_replace("&#10;","",$cmstyle);
		$cmstyle = str_replace("&#10;","",$cmstyle);
		$cmstyle = trim(preg_replace('/\s+/', ' ', $cmstyle));
		$cmstyle = str_replace("} ","}",$cmstyle);
		
		$cmstyle = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($cmstyle));
		$cmstyle = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($cmstyle));

		$cmstyle = str_replace(";",";&#10;    ",$cmstyle);
		$cmstyle = str_replace("}","}&#10;&#10;",$cmstyle);
		$cmstyle = str_replace("{","{&#10;    ",$cmstyle);
		$cmstyle = str_replace("&#10;    }","&#10;}",$cmstyle);
		$cmstyle = str_replace("     ","    ",$cmstyle);
		$cmstyle = str_replace("    }","}",$cmstyle);
		
	}
	
	if ($clearspaces){
		$cmstyle = str_replace("&#10;","",$cmstyle);
		$cmstyle = str_replace("&#10;","",$cmstyle);
		$cmstyle = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($cmstyle));
	}
	
	//$cmstyle = str_replace(" ",".",$cmstyle);
	return $cmstyle;
}