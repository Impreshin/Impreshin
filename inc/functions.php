<?php
function last_page() {
	$f3 = Base::instance();
	$user = $f3->get("user");
	$f3->get("DB")->exec("UPDATE global_users SET last_page = '" . $_SERVER['REQUEST_URI'] . "' WHERE ID = '" . $user['ID'] . "'");

	$app = $f3->get("app");
	$table = $app . "_users_settings";
	$f3->get("DB")->exec("UPDATE $table SET last_page = '" . $_SERVER['REQUEST_URI'] . "' WHERE uID = '" . $user['ID'] . "'");


	$st = array();
	$uID = $user['ID'];
	$cfg = $f3->get("CFG");
	foreach ($cfg['apps'] as $a) {
		$st[] = "COALESCE((SELECT last_page FROM " . $a . "_users_settings WHERE uID = '$uID'),'/$a') as $a";
	}
	$st = implode(",", $st);
	$st = $f3->get("DB")->exec("SELECT $st ");
	if (count($st)) $st = $st[0];

	foreach ($cfg['apps'] as $a) {
		if (substr($st[$a], 0, 3) != "/$a") {
			$st[$a] = "/$a";
		}
	}

	$f3->set("last_pages", $st);

	//test_array($app->get("last_pages"));
}


function access() {
	$app = Base::instance();
	$user = $app->get("user");
	if (!$user['ID']) $app->reroute("/login");

}

function isLocal() {
	if (file_exists("D:/web/local.txt")||file_exists("C:/web/local.txt")) {
		return true;
	} else return false;
}

function percentDiff($old, $new, $outputRendered=true) {
	$old = trim(preg_replace('#^<p>\s*.nbsp;<\/p>#', '', $old));
	$new = trim(preg_replace('#^<p>\s*.nbsp;<\/p>#', '', $new));


	$f3 = Base::instance();

	$o = $f3->scrub($old);
	$a = $f3->scrub($new);
	$t = s_text::diff($o, $a, " ");



	$return = array(
		"old"  => $old,
		"new"  => $new,
		"stats"=> $t['stats']
	);

	if ($outputRendered){
		$disp = s_text::diff($old, $new, " ");
		$return['html']= $disp['html'];
		$return['patch']= $disp['patch'];
	}
	return $return;
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




	$re = '%# Collapse whitespace everywhere but in blacklisted elements.
        (?>             # Match all whitespans other than single space.
          [^\S ]\s*     # Either one [\t\r\n\f\v] and zero or more ws,
        | \s{2,}        # or two or more consecutive-any-whitespace.
        ) # Note: The remaining regex consumes no text at all...
        (?=             # Ensure we are not in a blacklist tag.
          [^<]*+        # Either zero or more non-"<" {normal*}
          (?:           # Begin {(special normal*)*} construct
            <           # or a < starting a non-blacklist tag.
            (?!/?(?:textarea|pre|script)\b)
            [^<]*+      # more non-"<" {normal*}
          )*+           # Finish "unrolling-the-loop"
          (?:           # Begin alternation group.
            <           # Either a blacklist start tag.
            (?>textarea|pre|script)\b
          | \z          # or end of file.
          )             # End alternation group.
        )  # If we made it here, we are not in a blacklist tag.
        %Six';
	//$buffer = preg_replace($re, " ", $buffer);


	//if ($buffer === null) exit("PCRE Error! File too big.\n");
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

function money_format($format, $number) {
	$regex = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?' . '(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/';
	if (setlocale(LC_MONETARY, 0) == 'C') {
		setlocale(LC_MONETARY, '');
	}
	$locale = localeconv();
	preg_match_all($regex, $format, $matches, PREG_SET_ORDER);
	foreach ($matches as $fmatch) {
		$value = floatval($number);
		$flags = array('fillchar' => preg_match('/\=(.)/', $fmatch[1], $match) ? $match[1] : ' ', 'nogroup' => preg_match('/\^/', $fmatch[1]) > 0, 'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ? $match[0] : '+', 'nosimbol' => preg_match('/\!/', $fmatch[1]) > 0, 'isleft' => preg_match('/\-/', $fmatch[1]) > 0);
		$width = trim($fmatch[2]) ? (int)$fmatch[2] : 0;
		$left = trim($fmatch[3]) ? (int)$fmatch[3] : 0;
		$right = trim($fmatch[4]) ? (int)$fmatch[4] : $locale['int_frac_digits'];
		$conversion = $fmatch[5];

		$positive = true;
		if ($value < 0) {
			$positive = false;
			$value *= -1;
		}
		$letter = $positive ? 'p' : 'n';

		$prefix = $suffix = $cprefix = $csuffix = $signal = '';

		$signal = $positive ? $locale['positive_sign'] : $locale['negative_sign'];
		switch (true) {
			case $locale["{$letter}_sign_posn"] == 1 && $flags['usesignal'] == '+':
				$prefix = $signal;
				break;
			case $locale["{$letter}_sign_posn"] == 2 && $flags['usesignal'] == '+':
				$suffix = $signal;
				break;
			case $locale["{$letter}_sign_posn"] == 3 && $flags['usesignal'] == '+':
				$cprefix = $signal;
				break;
			case $locale["{$letter}_sign_posn"] == 4 && $flags['usesignal'] == '+':
				$csuffix = $signal;
				break;
			case $flags['usesignal'] == '(':
			case $locale["{$letter}_sign_posn"] == 0:
				$prefix = '(';
				$suffix = ')';
				break;
		}
		if (!$flags['nosimbol']) {
			$currency = $cprefix . ($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']) . $csuffix;
		} else {
			$currency = '';
		}
		$space = $locale["{$letter}_sep_by_space"] ? ' ' : '';

		$value = number_format($value, $right, $locale['mon_decimal_point'], $flags['nogroup'] ? '' : $locale['mon_thousands_sep']);
		$value = @explode($locale['mon_decimal_point'], $value);

		$n = strlen($prefix) + strlen($currency) + strlen($value[0]);
		if ($left > 0 && $left > $n) {
			$value[0] = str_repeat($flags['fillchar'], $left - $n) . $value[0];
		}
		$value = implode($locale['mon_decimal_point'], $value);
		if ($locale["{$letter}_cs_precedes"]) {
			$value = $prefix . $currency . $space . $value . $suffix;
		} else {
			$value = $prefix . $value . $space . $currency . $suffix;
		}
		if ($width > 0) {
			$value = str_pad($value, $width, $flags['fillchar'], $flags['isleft'] ? STR_PAD_RIGHT : STR_PAD_LEFT);
		}

		$format = str_replace($fmatch[0], $value, $format);
	}
	return $format;
}
function test_array($array,$splitter=","){

	if (!is_array($array)){
		$array = explode($splitter,$array);
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