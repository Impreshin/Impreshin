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
//require_once('inc/class.email.php');
//require_once('inc/class.store.php');


$app->set('AUTOLOAD', './|lib/|lib/pChart/class/|controllers/|controllers/ab/|controllers/ab/data/|controllers/nf/|controllers/nf/data/|controllers/setup/');
$app->set('PLUGINS', 'lib/f3/|lib/suga/');
$app->set('TZ', 'Africa/Johannesburg');
$app->set('DEBUG', 2);
$app->set('HIGHLIGHT', FALSE);
$app->set('UI', 'ui/;' . str_replace(array( "/", "\\" ), DIRECTORY_SEPARATOR, $cfg['upload']['folder']
)
);
//$app->set('EXTEND', TRUE);
//$app->set('UI', 'ui/');
//$app->set('TEMP', 'temp/');
$app->set('CACHE', false);

$app->set("exit", false);
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
$allowed[] = "setup";
$folder = (in_array($folder, $allowed)) ? $folder : "";

//test_array($folder);

$app->set('app', (isset($_GET['app']))?$_GET['app'] : $folder);
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

	if (in_array($folder, $cfg['apps'])){
		$app->get("DB")->exec("UPDATE " . $folder . "_users_settings SET  last_activity = now() WHERE uID = '" . $user['ID'] . "'");
	}


}

//test_array($user);

$app->set('user', $user);
$docs = array();
if (file_exists('docs/docs.php')) {
	require_once('docs/docs.php');
}
$app->set('docs', $docs);


if ($folder && $folder != "setup") {

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

//test_array($user);


$app->route('GET|POST /setup', function ($f3, $params) {
		$f3->chain('access; controllers\setup\controller_home->page');
	}
);

$app->route('GET|POST /setup/@company', function ($f3, $params) {
		$f3->chain('access; controllers\setup\controller_home->page');
	}
);
$app->route('GET|POST /setup/@company/@app', function ($f3, $params) {
		$f3->chain('access; controllers\setup\controller_home->page');
	}
);
$app->route('GET|POST /setup/@company/@app/@pID', function ($f3, $params) {
		$f3->chain('access; controllers\setup\controller_home->page');
	}
);
$app->route('GET|POST /setup/@company/@app/@pID/@section', function ($f3, $params) {
		//test_array($params);
		$f3->chain('access; controllers\setup\\controller_setup->page');
	}
);




$app->route('GET /min/css/@filename', 'general->css_min', $ttl);
$app->route('GET /min/css*', 'general->css_min', $ttl);
$app->route('GET /min/js/@filename', 'general->js_min', $ttl);
$app->route('GET /min/js*', 'general->js_min', $ttl);


$app->route('GET|POST /@app/upload/', 'general->upload');

$app->route('GET /charts/line', 'charts->line');

$app->route('GET|POST /logout', function ($app, $params) use ($user) {
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

			$app->reroute($last_app);
		} else {
			$app->reroute("/login?to=" . $_SERVER['REQUEST_URI']);
		}

	}
);
$app->route('GET|POST /noaccess', function () {
		echo "you dont have access for that app";
		die();
	}
);
$app->route('GET|POST /login', 'controllers\controller_login->page');
$app->route('GET /screenshots', 'controllers\controller_screenshots->page');
$app->route('GET /news', 'controllers\controller_news->page');
$app->route('GET /news/@item', 'controllers\controller_news->page');
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


$app->route('GET /ab/form', function ($f3, $params) use ($user) {
		//test_array($user);
		if ($user['permissions']['form']['new']) {
			$f3->chain('access; last_page; controllers\ab\controller_app_form->page');
		} else {
			$f3->error(404);
		}

	}
);
$app->route('GET /ab/form/@ID', function ($f3, $params) use ($user) {
		if ($user['permissions']['form']['edit'] || $user['permissions']['form']['edit_master'] || $user['permissions']['form']['delete']) {
			$f3->chain('access; last_page; controllers\ab\controller_app_form->page');
		} else {
			$f3->error(404);

		}
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
$app->route('GET /ab/admin/accounts/import', function ($f3, $params) {
		$f3->chain('access; last_page; controllers\ab\controller_admin_accounts_import->page');
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

$app->route("GET|POST /$folder/data/@function", function ($app, $params) use ($folder) {
		$app->call("controllers\\$folder\\data\\data->" . $params['function']);
	}
);
$app->route("GET|POST /$folder/data/@class/@function", function ($app, $params) use ($folder) {
		//test_array($params['function']);
		$app->call("controllers\\$folder\\data\\" . $params['class'] . "->" . $params['function']);
	}
);

$app->route("GET|POST /$folder/data/@folder/@class/@function", function ($app, $params) use ($folder) {
		$app->call("controllers\\$folder\\data\\" . $params['folder'] . "\\" . $params['class'] . "->" . $params['function']);
	}
);

$app->route("GET|POST /$folder/save/@function", function ($app, $params) use ($folder) {
		$app->call("controllers\\$folder\\save\\save->" . $params['function']);
	}
);
$app->route("GET|POST /$folder/save/@class/@function", function ($app, $params) use ($folder) {
		$app->call("controllers\\$folder\\save\\" . $params['class'] . "->" . $params['function']);
	}
);
$app->route("GET|POST /$folder/save/@folder/@class/@function", function ($app, $params) use ($folder) {
		$app->call("controllers\\$folder\\save\\" . $params['folder'] . "\\" . $params['class'] . "->" . $params['function']);
	}
);

$app->route("GET|POST /$folder/download/@folder/@ID/*", function ($app, $params) use ($folder) {
		$app->call("controllers\\$folder\\controller_general_download->" . $params['folder']);
	}
);


$app->route("GET|POST /$folder/thumb/@folder/@ID/*", function ($app, $params) use ($folder) {
		$app->call("controllers\\$folder\\controller_general_thumb->" . $params['folder']);

		/*
		 $app->mutex(function () use ($folder, $app, $params) {
				$app->call("controllers\\$folder\\controller_general_thumb->" . $params['folder']);
			}
		);
		*/
	}
);
$app->route("GET|POST /$folder/thumb/@folder/@ID", function ($app, $params) use ($folder) {
		$app->call("controllers\\$folder\\controller_general_thumb->" . $params['folder']);
		/*
		$app->mutex(function () use ($folder, $app, $params) {
				$app->call("controllers\\$folder\\controller_general_thumb->" . $app->get('PARAMS.folder'));
			}
		);
		*/
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

if ($app->get("exit")) {
	exit();
}


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

if ($folder && $folder != "setup") {
	$notificationmodel = "\\models\\$folder\\user_notifications";
	$GLOBALS["output"]['notifications'] = $notificationmodel::show();
}


//ob_start("ob_gzhandler");
if (((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || $app->get("showjson")) || !$app->get("__runTemplate")) {



	header("Content-Type: application/json");

	echo json_encode($GLOBALS["output"]);





} else {


	//test_array($GLOBALS['output']);

	//ob_start('ob_gzhandler');
	$timersbottom = '
					<script type="text/javascript">

				       updatetimerlist(' . json_encode($GLOBALS["output"]) . ');
					</script>
				';
	if (strpos($GLOBALS["render"], "<!--print version-->") || strpos($GLOBALS["render"], "<!--no_timer_list-->")) {
		echo $GLOBALS["render"];
	} else {
		echo str_replace("</body>", $timersbottom . '</body>', $GLOBALS["render"]);
	}



}
