<?php


date_default_timezone_set('Africa/Johannesburg');
setlocale(LC_MONETARY, 'en_ZA');

$GLOBALS["models"] = array();
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
require_once('inc/class.pagination.php');


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


$app->set('AUTOLOAD', './|lib/|lib/pChart/class/|controllers/|controllers/ab/|controllers/ab/data/');
$app->set('PLUGINS', 'lib/f3/|lib/suga/');
$app->set('CACHE', TRUE);
$app->set('DEBUG', 2);

$app->set('EXTEND', TRUE);
$app->set('UI', 'ui/');
$app->set('TEMP', 'tmp/');

$uri = $_SERVER['REQUEST_URI'];
$folder = "";
if ($uri) {
	$uri = explode("/", $uri);
	$folder = isset($uri[1]) ? $uri[1] : "";

}
$folder = strtolower($folder);

$allowed = array("ab");
$folder = (in_array($folder, $allowed)) ? $folder : "";

$app->set('app', $folder);


$cfg = array(
	"debug"=>array(
		"highlightfrom"=>0.5 // the debug timers thing
	),
	"DB"    => array(
		"host"    => "localhost",
		"username"=> "",
		"password"=> "",
		"database"=> "adbooker_v5"
	),
	"upload"=> array(
		"material"=> false,
		"pages"   => false,
		"folder"  => $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . $folder
	)
);
require_once('config.inc.php');


$app->set('DB', new DB('mysql:host=' . $cfg['DB']['host'] . ';dbname=' . $cfg['DB']['database'] . '',  $cfg['DB']['username'] , $cfg['DB']['password'] ));

unset($cfg['DB']);
$app->set('cfg', $cfg);


$app->set('system', new msg());


$version = '0.0.1';
$version = date("YmdH");
$minVersion = preg_replace("/[^0-9]/", "", $version);



$app->set('version', $version);
$app->set('v', $minVersion);
$user = "";

$uID = isset($_SESSION['uID'])?$_SESSION['uID']:"";
$username = isset($_POST['email'])?$_POST['email']:"";
$password = isset($_POST['password'])?$_POST['password']:"";
$userO = new \models\user();

if ($username && $password){
	$uID = $userO->login($username,$password);
}


if ($folder) {


	$settingsmodel = "\\models\\$folder\\settings";
	$app->set('settings', $settingsmodel::getSettings());
	$app->set('defaults', $settingsmodel::getDefaults());






}

$user = $userO->get($uID);
if (!$user['ID']&&$folder) {
	F3::reroute("/login?to=". $_SERVER['REQUEST_URI']);
}


$app->set('user', $user);







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

$app->route('GET|POST /logout', function() use ($user) {
		session_unset();
		//session_destroy();
		F3::reroute("/login");
	});

$app->route('GET|POST /', function() use ($user) {
		if ($user['ID']) {

			if (isset($_GET['to'])&& $_GET['to']){
				$last_app = $_GET['to'];
			} else {
				$last_app = $user['last_page'] ? $user['last_page'] : "";
				if (!$last_app) {
					$last_app = $user['last_app'] ? "/" . $user['last_app'] . "/" : "/ab/";
				}
			}

;

			F3::reroute($last_app);
		} else {
			F3::reroute("/login?to=". $_SERVER['REQUEST_URI']);
		}

	}
);
$app->route('GET|POST /login', 'controllers\controller_login->page');
$app->route('GET|POST /register', 'controllers\controller_register->page');

//include_once("/controllers/ab/_data.php");


$app->route('GET /ab/', 'controllers\ab\controller_provisional->page');
$app->route('GET /ab/production', 'controllers\ab\controller_production->page');
$app->route('GET /ab/layout', 'controllers\ab\controller_layout->page');
$app->route('GET /ab/overview', 'controllers\ab\controller_overview->page');
$app->route('GET /ab/records/search', 'controllers\ab\controller_search->page');
$app->route('GET /ab/records/deleted', 'controllers\ab\controller_deleted->page');
$app->route('GET /ab/form', 'controllers\ab\controller_form->page');
$app->route('GET /ab/form/@ID', 'controllers\ab\controller_form->page');


$app->route('GET /ab/test', 'controllers\ab\controller_test->page');


$app->route('GET|POST /ab/data/@function', function() use($app) {
		$app->call("controllers\\ab\\data\\data->" . $app->get('PARAMS.function'));
	}
);
$app->route('GET|POST /ab/data/@class/@function', function() use($app) {
		$app->call("controllers\\ab\\data\\" . $app->get('PARAMS.class') . "->" . $app->get('PARAMS.function'));
	}
);

$app->route('GET|POST /ab/save/@function', function() use($app) {
		$app->call("controllers\\ab\\save\\save->" . $app->get('PARAMS.function'));
	}
);
$app->route('GET|POST /ab/save/@class/@function', function() use($app) {
		$app->call("controllers\\ab\\save\\" . $app->get('PARAMS.class') . "->" . $app->get('PARAMS.function'));
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
$pageSize = ob_get_length();

ob_end_clean();

$models = $GLOBALS['models'];
//test_array($models);
$t = array();
foreach ($models as $model) {

	$c = array();
	foreach ($model['m'] as $method) {
		$c[] = $method;
	}


	$model['m'] = $c;
	$t[] = $model;
}
$models = $t;
//test_array($GLOBALS['models']);

$totaltime = $pageExecute->stop("Page Execute");
$GLOBALS["output"]['timer'] = $GLOBALS['timer'];
$GLOBALS["output"]['models'] = $models;
$GLOBALS["output"]['page'] = array(
	"page"=> $_SERVER['REQUEST_URI'],
	"time"=> $totaltime,
	"size"=> ($pageSize)
);


//ob_start("ob_gzhandler");
if (((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || F3::get("showjson")) || !F3::get("__runTemplate")) {


	ob_start('ob_gzhandler');
	header("Content-Type: application/json");

	echo json_encode($GLOBALS["output"]);

	exit();


} else {

	//ob_start('ob_gzhandler');
	;
	$timersbottom = '
					<script type="text/javascript">
				       updatetimerlist(' . json_encode($GLOBALS["output"]) . ');
					</script>
				';
	echo str_replace("</body>", $timersbottom . '</body>', $GLOBALS["render"]);
	exit();

}
//ob_end_flush();








