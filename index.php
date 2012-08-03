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
$cfg = array();
require_once('config.default.inc.php');
require_once('config.inc.php');
$GLOBALS['cfg'] = $cfg;

require_once('inc/class.timer.php');
$pageExecute = new timer(true);

require_once('inc/functions.php');
require_once('inc/class.pagination.php');
//test_array(array("HTTP_HOST"  => $_SERVER['HTTP_HOST'], "REQUEST_URI"=> $_SERVER['REQUEST_URI']));









$app = require('lib/f3/base.php');
require_once('lib/Twig/Autoloader.php');
Twig_Autoloader::register();
require_once('inc/class.msg.php');
require_once('inc/class.template.php');
require_once('inc/class.email.php');
require_once('inc/class.store.php');




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

	if (strpos($folder,"?")){
		$folder = explode("?", $folder);
		$folder = isset($folder[0]) ? $folder[0] : "";
	}



}
$folder = strtolower($folder);







$allowed = $cfg['apps'];
$folder = (in_array($folder, $allowed)) ? $folder : "";

//test_array($folder);

$app->set('app', $folder);
$app->set('DB', new DB('mysql:host=' . $cfg['DB']['host'] . ';dbname=' . $cfg['DB']['database'] . '',  $cfg['DB']['username'] , $cfg['DB']['password'] ));





$app->set('cfg', $cfg);











$app->set('system', new msg());


$version = '0.0.2';
$version = date("YmdH");
$minVersion = preg_replace("/[^0-9]/", "", $version);



$app->set('version', $version);
$app->set('v', $minVersion);
$user = "";

$uID = isset($_SESSION['uID'])?$_SESSION['uID']:"";
$username = isset($_POST['login_email'])?$_POST['login_email']:"";
$password = isset($_POST['login_password'])?$_POST['login_password']:"";



$userO = new \models\user();

if ($username && $password){
	$uID = $userO->login($username,$password);
	F3::reroute("/");
}


$user = $userO->user($uID);
if (!$user['ID']&&$folder) {
	F3::reroute("/login?to=". $_SERVER['REQUEST_URI']);
}
if ($folder && $user['ID']){

	F3::get("DB")->exec("UPDATE global_users SET last_app = '$folder', last_activity = now() WHERE ID = '" . $user['ID'] . "'");


	F3::get("DB")->exec("UPDATE " . $folder . "_users_settings SET  last_activity = now() WHERE uID = '" . $user['ID'] . "'");
}

$app->set('user', $user);


if ($folder) {

	$settingsmodel = "\\models\\$folder\\settings";
	$app->set('settings', $settingsmodel::getSettings($user['permissions']));
	$app->set('defaults', $settingsmodel::getDefaults());


}

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


$app->route('GET|POST /@app/upload/', 'general->upload');

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
$app->route('GET|POST /noaccess', function(){
		echo "you dont have access for that app";
		exit();
	});
$app->route('GET|POST /login', 'controllers\controller_login->page');
$app->route('GET|POST /screenshots', 'controllers\controller_screenshots->page');
$app->route('GET|POST /history', 'controllers\controller_history->page');
$app->route('GET|POST /history/commits', 'controllers\controller_history->getCommits');
$app->route('GET|POST /about', 'controllers\controller_about->page');
$app->route('GET /data/keepalive', function() use ($user){


		$last_activity =  new DateTime($user['last_activity']);
		$now = new DateTime('now');

		$interval = $last_activity->diff($now);
		$diff = (($interval->h*60)*60)+ ($interval->i * 60)+ ($interval->s);

		//$interval['diff']=$diff;



		if (isset($_GET['keepalive'])&& $_GET['keepalive']){
			F3::get("DB")->exec("UPDATE global_users SET last_activity = now() WHERE ID = '" . $user['ID'] . "'");
			$diff = 0;
			// upadate the last_activity
		}
		$t = array(
			"ID"=>$user['ID'],
			"idle"=>$diff
		);

		test_array($t);

	});

//include_once("/controllers/ab/_data.php");
// --------------------------------------------------------------------------------

function last_page(){

	$user = F3::get("user");
	F3::get("DB")->exec("UPDATE global_users SET last_page = '" . $_SERVER['REQUEST_URI'] . "' WHERE ID = '" . $user['ID'] . "'");
}
function access(){

	$user = F3::get("user");
	if (!$user['ID']) F3::reroute("/login");
}

$app->route('GET /ab', 'access; last_page; controllers\ab\controller_app_provisional->page');

$app->route('GET /ab/print/details', 'access; controllers\ab\controller_app_details->_print');

$app->route('GET /ab/provisional', 'access; last_page; controllers\ab\controller_app_provisional->page');
$app->route('GET /ab/print/provisional', 'access; controllers\ab\controller_app_provisional->_print');

$app->route('GET /ab/production', 'access; last_page; controllers\ab\controller_app_production->page');
$app->route('GET /ab/print/production', 'access; controllers\ab\controller_app_production->_print');


$app->route('GET /ab/layout', 'access; last_page; controllers\ab\controller_app_layout->page');
$app->route('GET /ab/overview', 'access; last_page; controllers\ab\controller_app_overview->page');


$app->route('GET /ab/records/search', 'access; last_page; controllers\ab\controller_app_search->page');
$app->route('GET /ab/print/search', 'access; controllers\ab\controller_app_search->_print');

$app->route('GET /ab/records/deleted', 'access; last_page; controllers\ab\controller_app_deleted->page');
$app->route('GET /ab/print/deleted', 'access; controllers\ab\controller_app_deleted->_print');


$app->route('GET /ab/form', 'access; last_page; controllers\ab\controller_app_form->page');
$app->route('GET /ab/form/@ID', 'access; last_page; controllers\ab\controller_app_form->page');

// --------------------------------------------------------------------------------

$app->route('GET /ab/admin/dates', 'access; last_page;  controllers\ab\controller_admin_dates->page');
$app->route('GET /ab/admin/users', 'access; last_page; controllers\ab\controller_admin_users->page');
$app->route('GET /ab/admin/accounts', 'access; last_page; controllers\ab\controller_admin_accounts->page');
$app->route('GET /ab/admin/accounts/status', 'access; last_page; controllers\ab\controller_admin_accounts_status->page');
$app->route('GET /ab/admin/sections', 'access; last_page; controllers\ab\controller_admin_sections->page');
$app->route('GET /ab/admin/categories', 'access; last_page; controllers\ab\controller_admin_categories->page');
$app->route('GET /ab/admin/marketers', 'access; last_page; controllers\ab\controller_admin_marketers->page');
$app->route('GET /ab/admin/marketers/targets', 'access; last_page; controllers\ab\controller_admin_marketers_targets->page');
$app->route('GET /ab/admin/production', 'access; last_page; controllers\ab\controller_admin_production->page');
$app->route('GET /ab/admin/placing', 'access; last_page; controllers\ab\controller_admin_placing->page');
$app->route('GET /ab/admin/placing/colours', 'access; last_page; controllers\ab\controller_admin_placing_colours->page');
$app->route('GET /ab/admin/loading', 'access; last_page; controllers\ab\controller_admin_loading->page');
$app->route('GET /ab/admin/inserts_types', 'access; last_page; controllers\ab\controller_admin_inserts_types->page');

$app->route('GET /ab/admin/publications', 'access; last_page; controllers\ab\controller_admin_publications->page');


// --------------------------------------------------------------------------------

$app->route('GET /ab/reports/publication/figures', 'access; last_page; controllers\ab\controller_reports_publication_figures->page');
$app->route('GET /ab/reports/publication/discounts', 'access; last_page; controllers\ab\controller_reports_publication_discounts->page');
$app->route('GET /ab/reports/publication/section', 'access; last_page; controllers\ab\controller_reports_publication_section_figures->page');
$app->route('GET /ab/reports/publication/placing', 'access; last_page; controllers\ab\controller_reports_publication_placing_figures->page');

$app->route('GET /ab/reports/account/figures', 'access; last_page; controllers\ab\controller_reports_account_figures->page');
$app->route('GET /ab/reports/account/discounts', 'access; last_page; controllers\ab\controller_reports_account_discounts->page');

$app->route('GET /ab/reports/marketer/figures', 'access; last_page; controllers\ab\controller_reports_marketer_figures->page');
$app->route('GET /ab/reports/marketer/discounts', 'access; last_page; controllers\ab\controller_reports_marketer_discounts->page');

$app->route('GET /ab/reports/production/figures', 'access; last_page; controllers\ab\controller_reports_production_figures->page');

$app->route('GET /ab/reports/category/figures', 'access; last_page; controllers\ab\controller_reports_category_figures->page');
$app->route('GET /ab/reports/category/discounts', 'access; last_page; controllers\ab\controller_reports_category_discounts->page');




// --------------------------------------------------------------------------------

$app->route('GET /ab/test', 'access; controllers\ab\controller_test->page');



$app->route('GET|POST /ab/data/@function', function() use($app) {
		$app->call("controllers\\ab\\data\\data->" . $app->get('PARAMS.function'));
	}
);
$app->route('GET|POST /ab/data/@class/@function', function() use($app) {
		$app->call("controllers\\ab\\data\\" . $app->get('PARAMS.class') . "->" . $app->get('PARAMS.function'));
	}
);

$app->route('GET|POST /ab/data/@folder/@class/@function', function() use($app) {
		$app->call("controllers\\ab\\data\\" . $app->get('PARAMS.folder') . "\\" . $app->get('PARAMS.class') . "->" . $app->get('PARAMS.function'));
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

$app->route('GET|POST /ab/download/@folder/@ID/*', function() use($app) {
		$app->call("controllers\\ab\\controller_general_download->" . $app->get('PARAMS.folder'));
	}
);
$app->route('GET|POST /ab/thumb/@folder/@ID/*', function() use($app) {
	F3::mutex(function() {
		F3::call("controllers\\ab\\controller_general_thumb->" . F3::get('PARAMS.folder'));
	});
	}
);



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

if ($folder){
	$GLOBALS["output"]['notifications'] = \models\ab\user_notifications::show();
}




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
	if (strpos($GLOBALS["render"],"<!--print version-->")==-1){
		echo str_replace("</body>", $timersbottom . '</body>', $GLOBALS["render"]);
	} else {
		echo $GLOBALS["render"];
	}

	exit();

}









