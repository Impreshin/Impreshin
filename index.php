<?php


date_default_timezone_set('Africa/Johannesburg');
setlocale(LC_MONETARY, 'en_ZA');
//ini_set('memory_limit', '256M');


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
$app = require('lib/f3/base.php');
require_once('inc/class.timer.php');
$pageExecute = new timer(true);


//test_array(array("HTTP_HOST"  => $_SERVER['HTTP_HOST'], "REQUEST_URI"=> $_SERVER['REQUEST_URI']));



require_once('inc/functions.php');
require_once('inc/class.pagination.php');
require_once('lib/Twig/Autoloader.php');
Twig_Autoloader::register();
require_once('inc/class.msg.php');
require_once('inc/class.template.php');
require_once('inc/class.email.php');
require_once('inc/class.store.php');


$app->set('AUTOLOAD', './|lib/|lib/pChart/class/|controllers/|controllers/ab/|controllers/ab/data/|controllers/nf/|controllers/nf/data/');
$app->set('PLUGINS', 'lib/f3/|lib/suga/');
//$app->set('CACHE', TRUE);
$app->set('DEBUG', 2);

//$app->set('EXTEND', TRUE);
//$app->set('UI', 'ui/');
//$app->set('TEMP', 'temp/');


$uri = $_SERVER['REQUEST_URI'];
$folder = "";
if ($uri) {
	$uri = explode("/", $uri);
	$folder = isset($uri[1]) ? $uri[1] : "";

	if (strpos($folder, "?")) {
		$folder = explode("?", $folder);
		$folder = isset($folder[0]) ? $folder[0] : "";
	}


}
$folder = strtolower($folder);


$allowed = $cfg['apps'];
$folder = (in_array($folder, $allowed)) ? $folder : "";

//test_array($folder);

$app->set('app', $folder);
$app->set('DB', new DB\SQL('mysql:host=' . $cfg['DB']['host'] . ';dbname=' . $cfg['DB']['database'] . '', $cfg['DB']['username'], $cfg['DB']['password']));


$app->set('cfg', $cfg);


$app->set('system', new msg());

$version = '0.0.6';
$version = date("YmdH");
$minVersion = preg_replace("/[^0-9]/", "", $version);


$app->set('version', $version);
$app->set('v', $minVersion);
$user = "";

$uID = isset($_SESSION['uID']) ? $_SESSION['uID'] : "";
$username = isset($_POST['login_email']) ? $_POST['login_email'] : "";
$password = isset($_POST['login_password']) ? $_POST['login_password'] : "";


$userO = new \models\user();


if ($username && $password) {
	$uID = $userO->login($username, $password);
	$app->reroute("/");
}


$user = $userO->user($uID);
if (!$user['ID'] && $folder) {
	$app->reroute("/login?to=" . $_SERVER['REQUEST_URI']);
}
if ($folder && $user['ID']) {

	$app->get("DB")->exec("UPDATE global_users SET last_app = '$folder', last_activity = now() WHERE ID = '" . $user['ID'] . "'");


	$app->get("DB")->exec("UPDATE " . $folder . "_users_settings SET  last_activity = now() WHERE uID = '" . $user['ID'] . "'");
}

$app->set('user', $user);
$docs = array();
if (file_exists('docs/docs.php')) {
	require_once('docs/docs.php');
}
$app->set('docs', $docs);


if ($folder) {

	$settingsmodel = "\\models\\$folder\\settings";
	$app->set('settings', $settingsmodel::settings($user['permissions']));
	$app->set('defaults', $settingsmodel::defaults());


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

$app->route('GET|POST /logout', function ($app,$params) use ($user) {
		session_unset();
		//session_destroy();
		$app->reroute("/login");
	}
);

$app->route('GET|POST /', function ($app) use ($user) {
		if ($user['ID']) {

			if (isset($_GET['to']) && $_GET['to']) {
				$last_app = $_GET['to'];
			} else {
				$last_app = $user['last_page'] ? $user['last_page'] : "";
				if (!$last_app) {
					$last_app = $user['last_app'] ? "/" . $user['last_app'] . "/" : "/ab/";
				}
			}


			;

			$app->reroute($last_app);
		} else {
			$app->reroute("/login?to=" . $_SERVER['REQUEST_URI']);
		}

	}
);
$app->route('GET|POST /noaccess', function () {
		echo "you dont have access for that app";
		exit();
	}
);
$app->route('GET|POST /login', 'controllers\controller_login->page');
$app->route('GET /screenshots', 'controllers\controller_screenshots->page');
$app->route('GET /history', 'controllers\controller_history->page');
$app->route('GET /history/commits', 'controllers\controller_history->getCommits');
$app->route('GET /about', 'controllers\controller_about->page');

$app->route('GET|POST /@app/help', 'controllers\controller_docs->help_page');
$app->route('GET|POST /@app/help/@section', 'controllers\controller_docs->section_page');
$app->route('GET|POST /@app/help/@section/@sub_section', 'controllers\controller_docs->sub_section_page');
$app->route('GET|POST /@app/help/@section/@sub_section/@item', 'controllers\controller_docs->sub_section_item_page');
$app->route('GET|POST /@app/help/@section/@sub_section/@item/*', 'controllers\controller_docs->sub_section_item_page');


$app->route('GET /data/keepalive', function ($app, $params) use ($user) {


		$last_activity = new DateTime($user['last_activity']);
		$now = new DateTime('now');

		$interval = $last_activity->diff($now);
		$diff = (($interval->h * 60) * 60) + ($interval->i * 60) + ($interval->s);

		//$interval['diff']=$diff;


		if (isset($_GET['keepalive']) && $_GET['keepalive']) {
			$app->get("DB")->exec("UPDATE global_users SET last_activity = now() WHERE ID = '" . $user['ID'] . "'");
			$diff = 0;
			// upadate the last_activity
		}
		$t = array(
			"ID"   => $user['ID'],
			"idle" => $diff
		);

		test_array($t);

	}
);

// --------------------------------------------------------------------------------


function last_page() {
	$f3= Base::instance();
	$user = $f3->get("user");
	$f3->get("DB")->exec("UPDATE global_users SET last_page = '" . $_SERVER['REQUEST_URI'] . "' WHERE ID = '" . $user['ID'] . "'");

	$app = $f3->get("app");
	$table = $app . "_users_settings";
	$f3->get("DB")->exec("UPDATE $table SET last_page = '" . $_SERVER['REQUEST_URI'] . "' WHERE uID = '" . $user['ID'] . "'");


	$st = array();
	$uID = $user['ID'];
	$cfg = $f3->get("cfg");
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


$app->route('GET /ab', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_app_provisional->page');
	}
);

$app->route('GET /ab/print/details', function () use ($app) {
		access();
		$app->call('controllers\ab\controller_app_details->_print');
	}
);

$app->route('GET /ab/provisional', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_app_provisional->page');
	}
);
$app->route('GET /ab/print/provisional', function ($f3, $params) {
		$f3->chain('access; controllers\ab\controller_app_provisional->_print');
	}
);

$app->route('GET /ab/production', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_app_production->page');
	}
);
$app->route('GET /ab/print/production', function ($f3, $params) {
		$f3->chain('access; controllers\ab\controller_app_production->_print');
	}
);


$app->route('GET /ab/layout', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_app_layout->page');
	}
);
$app->route('GET /ab/overview', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_app_overview->page');
	}
);


$app->route('GET /ab/records/search', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_app_search->page');
	}
);
$app->route('GET /ab/print/search', function ($f3, $params) {
		$f3->chain('access; controllers\ab\controller_app_search->_print');
	}
);

$app->route('GET /ab/records/deleted', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_app_deleted->page');
	}
);
$app->route('GET /ab/print/deleted', function ($f3, $params) {
		$f3->chain('access; controllers\ab\controller_app_deleted->_print');
	}
);


$app->route('GET /ab/form', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_app_form->page');
	}
);
$app->route('GET /ab/form/@ID', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_app_form->page');
	}
);

// --------------------------------------------------------------------------------

$app->route('GET /ab/admin/dates', function ($f3, $params) {
		$f3->chain('access; last_page;  controllers\ab\controller_admin_dates->page');
	}
);
$app->route('GET /ab/admin/users', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_admin_users->page');
	}
);
$app->route('GET /ab/admin/accounts', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_admin_accounts->page');
	}
);
$app->route('GET /ab/admin/accounts/status', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_admin_accounts_status->page');
	}
);
$app->route('GET /ab/admin/sections', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_admin_sections->page');
	}
);
$app->route('GET /ab/admin/categories', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_admin_categories->page');
	}
);
$app->route('GET /ab/admin/marketers', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_admin_marketers->page');
	}
);
$app->route('GET /ab/admin/marketers/targets', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_admin_marketers_targets->page');
	}
);
$app->route('GET /ab/admin/production', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_admin_production->page');
	}
);
$app->route('GET /ab/admin/placing', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_admin_placing->page');
	}
);
$app->route('GET /ab/admin/placing/colours', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_admin_placing_colours->page');
	}
);
$app->route('GET /ab/admin/loading', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_admin_loading->page');
	}
);
$app->route('GET /ab/admin/inserts_types', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_admin_inserts_types->page');
	}
);

$app->route('GET /ab/admin/publications', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_admin_publications->page');
	}
);


// --------------------------------------------------------------------------------

$app->route('GET /ab/reports/publication/figures', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_reports_publication_figures->page');
	}
);
$app->route('GET /ab/reports/publication/discounts', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_reports_publication_discounts->page');
	}
);
$app->route('GET /ab/reports/publication/section', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_reports_publication_section_figures->page');
	}
);
$app->route('GET /ab/reports/publication/placing', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_reports_publication_placing_figures->page');
	}
);

$app->route('GET /ab/reports/account/figures', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_reports_account_figures->page');
	}
);
$app->route('GET /ab/reports/account/discounts', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_reports_account_discounts->page');
	}
);

$app->route('GET /ab/reports/marketer/figures', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_reports_marketer_figures->page');
	}
);
$app->route('GET /ab/reports/marketer/discounts', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_reports_marketer_discounts->page');
	}
);
$app->route('GET /ab/reports/marketer/targets', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_reports_marketer_targets->page');
	}
);

$app->route('GET /ab/reports/production/figures', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_reports_production_figures->page');
	}
);

$app->route('GET /ab/reports/category/figures', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_reports_category_figures->page');
	}
);
$app->route('GET /ab/reports/category/discounts', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_reports_category_discounts->page');
	}
);


// --------------------------------------------------------------------------------

$app->route('GET /ab/test', function ($f3, $params) {
		$f3->chain('access; controllers\ab\controller_test->page');
	}
);


$app->route("GET|POST /$folder/logs/@function", function () use ($app) {
		$folder = $app->get("app");
		$section = $app->get('PARAMS.function');

		$return = array();

		$user = $app->get("user");
		$cID = $user['company']['ID'];

		$where = "cID='$cID' AND section='$section'";
		if (!in_array($section, array(""))) {
			$where .= " AND app = '$folder'";
		}
		$return = \models\logging::getAll($where, "datein DESC");

		return $GLOBALS["output"]['data'] = $return;


	}
);

$app->route("GET|POST /$folder/data/@function", function ($app, $params) {
		$folder = $app->get("app");

		$app->call("controllers\\$folder\\data\\data->" . $params['function']);
	}
);
$app->route("GET|POST /$folder/data/@class/@function", function ($app, $params) {
		$folder = $app->get("app");
		//test_array($params['function']);
		$app->call("controllers\\$folder\\data\\" . $params['class'] . "->" . $params['function']);
	}
);

$app->route("GET|POST /$folder/data/@folder/@class/@function", function ($app, $params) {
		$folder = $app->get("app");
		$app->call("controllers\\$folder\\data\\" . $params['folder'] . "\\" . $params['class'] . "->" . $params['function']);
	}
);

$app->route("GET|POST /$folder/save/@function", function ($app, $params) {
		$folder = $app->get("app");
		$app->call("controllers\\$folder\\save\\save->" . $params['function']);
	}
);
$app->route("GET|POST /$folder/save/@class/@function", function ($app, $params) {
		$folder = $app->get("app");
		$app->call("controllers\\$folder\\save\\" . $params['class'] . "->" . $params['function']);
	}
);

$app->route("GET|POST /$folder/download/@folder/@ID/*", function ($app, $params) {
		$folder = $app->get("app");
		$app->call("controllers\\$folder\\controller_general_download->" . $params['folder']);
	}
);


$app->route("GET|POST /$folder/thumb/@folder/@ID/*", function ($app, $params) {
		$folder = $app->get("app");
		$app->mutex(function () use ($folder, $app, $params) {
				$app->call("controllers\\$folder\\controller_general_thumb->" . $params['folder']);
			}
		);
	}
);
$app->route("GET|POST /$folder/thumb/@folder/@ID", function ($app, $params) {
		$folder = $app->get("app");
		$app->mutex(function () use ($folder, $app, $params) {
				$app->call("controllers\\$folder\\controller_general_thumb->" . $app->get('PARAMS.folder'));
			}
		);
	}
);
// --------------------------------------------------------------------------------


$app->route('GET /nf', 'access; last_page; controllers\nf\controller_app_provisional->page');
$app->route('GET /nf/provisional', 'access; last_page; controllers\nf\controller_app_provisional->page');
$app->route('GET /nf/production', 'access; last_page; controllers\nf\controller_app_production->page');
$app->route('GET /nf/form', 'access; last_page; controllers\nf\controller_app_form->page');
$app->route('GET /nf/form/@ID', 'access; last_page; controllers\nf\controller_app_form->page');
$app->route('GET /nf/form/article/@ID', 'access; last_page; controllers\nf\controller_app_form->article');

$app->route('GET|POST /nf/records12345', function () use ($app) {
		include_once("old_to_new/nf.php");
		$t = new nf_import();
		$t->records();


	}
);
$app->route('GET|POST /nf/import12345', function () use ($app) {
		include_once("old_to_new/nf.php");
		$t = new nf_import();
		$t->users();


	}
);


$app->route('GET /php', function () {
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
	"page" => $_SERVER['REQUEST_URI'],
	"time" => $totaltime,
	"size" => ($pageSize)
);

if ($folder) {
	$notificationmodel = "\\models\\$folder\\user_notifications";
	$GLOBALS["output"]['notifications'] = $notificationmodel::show();
}




//ob_start("ob_gzhandler");
if (((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || $app->get("showjson")) || !$app->get("__runTemplate")) {


	ob_start('ob_gzhandler');
	header("Content-Type: application/json");

	echo json_encode($GLOBALS["output"]);

	exit();


} else {


	//test_array($GLOBALS['output']);

	//ob_start('ob_gzhandler');
	$timersbottom = '
					<script type="text/javascript">

				       updatetimerlist(' . json_encode($GLOBALS["output"]) . ');
					</script>
				';
	if (strpos($GLOBALS["render"], "<!--print version-->") ) {
		echo $GLOBALS["render"];
	} else {
		echo str_replace("</body>", $timersbottom . '</body>', $GLOBALS["render"]);
	}

	//exit();

}


// checks to see if the cookie is set, if not then leaves it blank - tells the next step that it can decide if it should show the mobile site or not
$mobile_cookie = isset($_COOKIE['mobile'])? $_COOKIE['mobile'] : "";
// check if we must force the mobile or not based ona  cookie value... cookies are nice and tasty... with milk...
$force_mobile = ($mobile_cookie == "true") ? true : false;
if (isset($_GET['mobile'])) {
	if ($_GET['mobile'] == 'true') { // must we force the mobile site? if ?mobile=true then FORCE THAT MOBILE
		setcookie("mobile", "true", time() + 31536000, "/");
		$force_mobile = true;
	} else { // if ?mobile=false then remove the force.. become powerless.. crawl up ina  corner and die.......
		setcookie("mobile", "false");
		$force_mobile = false;
	}
}

if ($force_mobile){ // if we forcing the mobile part.. either via cookie or ?mobile=true then dont pass go.. dont collect R200.. go straight to the mobi site
	header("Location:http://www.mobisite.mobi");
} else { // no forcing happening.. its a democracy... like SA...kinda
	if ($detect->isMobile()){ // is the device a mobile.. if not then dont do anything.. aka stay on the current page
		if ($mobile_cookie == "" && !$detect->isTablet()){ // if it is a mobile.. and we dont have a cookie for it yet.. and its not a tablet.. then go to the mobie site... theres cookies and milk there.. with little chocolate soldiers you can bite their heads off
			header("Location:http://www.mobisite.mobi");
		}
	}
}

/*

steps it takes:
	1. check if theres a mobile cookie..
	2. checks if it must force mobile based on cookie or not
	3. if ?mobile is set it overwrites the cookie force stuff with its own
	4. check if we forcing mobile or not
		4.1 if we forcing the mobile site then go there, no other tests necessary
		4.2 does some mobile checks
			4.2.1 if its a mobile...
				4.2.1.1 the mobile cookie isnt set (aka havent visited the page before - otherwise it woulda forced the mobile / not in earlier steps). its also not a tablet - so if all goes well.. it should be its a mobile.. its not a tablet.. and we havent seen this client before / they havent messed around with forcing mobile. redirect that bitch


*/




$not_mobile_cookie = isset($_COOKIE['mobile']) ? true : false;
if (isset($_GET['mobile'])) $not_mobile_cookie = $_GET['mobile'];


if ($not_mobile_cookie == false && $detect->isMobile()) {
	if (!$detect->isTablet()) {
		header("Location:http://www.mobisite.mobi");
	}
}





