<?php


date_default_timezone_set('Africa/Johannesburg');
setlocale(LC_MONETARY, 'en_ZA');

$GLOBALS["output"] = array();
$GLOBALS["render"] = "";
if (session_id() == "") {
	$SID = @session_start();
} else $SID = session_id();
if (!$SID) {
	session_start();
	$SID = session_id();
}

require_once('inc/class.timer.php');
$pageExecute = new timer(true);


require_once('inc/functions.php');
require_once('inc/class.timer.php');


$app = require('lib/f3/base.php');
require_once('lib/Twig/Autoloader.php');
Twig_Autoloader::register();
require_once('inc/class.msg.php');
require_once('inc/class.template.php');
require_once('inc/class.email.php');
require_once('inc/class.store.php');

$version = '0.1.20';

$app->set('version', $version);
$app->set('v', preg_replace("/[^0-9]/", "", $version));


$app->set('AUTOLOAD', './|lib/|lib/pChart/class/|controllers/|controllers/ab/');
$app->set('PLUGINS', 'lib/f3/|lib/suga/');
$app->set('CACHE', TRUE);
$app->set('DEBUG', 2);

$app->set('EXTEND', TRUE);
$app->set('UI', 'ui/');
$app->set('TEMP', 'tmp/');


if (isLocal()) {
	$app->set('DB', new DB('mysql:host=localhost;dbname=adbooker_v5', '', ''));
	$app->set("GOOGLE_ANALYTICS", "");
	$app->set('MEDIA_folder', 'D:/Web/MeetPad');

} else {
	$app->set('DB', new db('mysql:host=localhost;dbname=adbooker_v5', 'website', 'zoutnet015'));
	$app->set("GOOGLE_ANALYTICS", "");
	$app->set('MEDIA_folder', 'C:/Web/adbooker_media');
}

	$app->set('system', new msg());


		$version = '0.0.1';
		$version = date("YmdH");
		$minVersion = preg_replace("/[^0-9]/", "", $version);

		$app->set('version', $version);
		$app->set('v', $minVersion);
		$app->set('ab_settings', \models\settings::getSettings("ab"));
		$app->set('ab_defaults', \models\settings::getDefaults("ab"));


		$uID = "21";

		$userO = new \models\user();
		$user = $userO->get($uID);

		if ((isset($_GET['apID'])&&$_GET['apID'])&&$_GET['apID']!=$user['ab_pID']){
			$user = $userO->get(\models\user::save_config(array("pID"=> $_GET['apID']), "ab", $uID));
		}
		$app->set('user', $user);


//test_array($user);


		ob_start();



		$ttl = 0;
		if (strpos($_SERVER['HTTP_HOST'], "dev.") === true || isLocal()) {
			$ttl = 0;
		}
		$ttl = 0;
		$app->route('GET /min/css/@filename', 'general->css_min', $ttl);
		$app->route('GET /min/css*', 'general->css_min', $ttl);
		$app->route('GET /min/js/@filename', 'general->js_min', $ttl);
		$app->route('GET /min/js*', 'general->js_min', $ttl);

		$app->route('GET /charts/line', 'charts->line');

		$app->route('GET /', function() {
				F3::reroute("/ab/");
			}
		);


		$app->route('GET /ab/', 'controllers\ab\provisional->page');
		$app->route('GET /ab/form', 'controllers\ab\form->page');
		$app->route('GET /ab/form/@ID', 'controllers\ab\form->page');

		$app->route('GET|POST /ab/data/@data', function() use($app) {
				$app->call('controllers\ab\data->' . $app->get('PARAMS.data'));
			}
		);
		$app->route('GET|POST /ab/save/@data', function() use($app) {
				$app->call('controllers\ab\save->' . $app->get('PARAMS.data'));
			}
		);

		//$app->route('GET|POST /ab/data/@data', 'abdata->{{@PARAMS.data}}');
		//$app->route('GET|POST /ab/save/@section', 'ab_controllers_save->{{@PARAMS.section}}');
		//$app->route('GET|POST /ab/data/bookings', 'ab_data->bookings');


		$app->route('GET /php', function() {
				phpinfo();
				exit();
			}
		);


		$app->run();

		$GLOBALS["render"] = ob_get_contents();
		ob_end_clean();


		$totaltime = $pageExecute->stop("Page Execute");
		$GLOBALS["output"]['timer'] = $GLOBALS['timer'];
		$GLOBALS["output"]['page'] = array(
			"page"=> $_SERVER['REQUEST_URI'],
			"time"=> $totaltime
		);

		//ob_start("ob_gzhandler");
		if ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || F3::get("showjson")) {
			header("Content-Type: application/json");
			exit(json_encode($GLOBALS["output"]));
		} else {
			if (F3::get("__runTemplate")) {
				;
				$timersbottom = '
					<script type="text/javascript">
				       updatetimerlist(' . json_encode($GLOBALS["output"]) . ');
					</script>
				';
				echo str_replace("</body>", $timersbottom . '</body>', $GLOBALS["render"]);
			} else {
				header("Content-Type: application/json");
				echo json_encode($GLOBALS["output"]);
			}
		}
	//ob_end_flush();









?>