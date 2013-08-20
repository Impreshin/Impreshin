<?php
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
// loading the config and default config
$cfg = array();
require_once('config.default.inc.php');
require_once('config.inc.php');
date_default_timezone_set($cfg['TZ']);
$GLOBALS['cfg'] = $cfg;
//$app = require('lib/f3/base.php');



require_once('inc/functions.php');
require_once('inc/app.php'); // core
require_once('inc/class.pagination.php');
require_once('lib/Twig/Autoloader.php');
Twig_Autoloader::register();
require_once('inc/class.template.php');

// the auto loader folders
$autoload = array(
	"./",
	"lib/",
	"controllers/"
);

$app = new core\app();

// loop through each app in the apps directory to build up the auto load string and app list
$apps = array();
foreach (glob("./apps/*", GLOB_ONLYDIR) as $folder) {
	$folder = trim($folder,"./");
	$apps[] = str_replace("apps/","",$folder);
	$folder = $folder . "/";
	$autoload[] = $folder;
	$autoload[] = $folder."controllers/";
	$autoload[] = $folder."models/";
}
//test_array($autoload);
// read the current git version string and convert it to numbers to be used for cache busting
$version = date("YmdH");
if (file_exists("./.git/refs/heads/" . $cfg['git']['branch'])) {
	$version = file_get_contents("./.git/refs/heads/" . $cfg['git']['branch']);
	$version = substr(base_convert(md5($version), 16, 10), -10);
}




$app->set('AUTOLOAD', implode("|", $autoload));
$app->set('PLUGINS', 'lib/f3/');
$app->set('TZ', $cfg['TZ']);
$app->set('DEBUG', 2);
$app->set('HIGHLIGHT', FALSE);

$app->set('UI', 'ui/;' . str_replace(array("/", "\\"), DIRECTORY_SEPARATOR, $cfg['upload']['folder']));
$app->set('CACHE', false);

$app->set('DB', new DB\SQL('mysql:host=' . $cfg['DB']['host'] . ';dbname=' . $cfg['DB']['database'] . '', $cfg['DB']['username'], $cfg['DB']['password']));

$app->set('CFG', $cfg);
$app->set('VERSION', $version);


$uID = isset($_SESSION['uID']) ? $_SESSION['uID'] : "";
$username = isset($_POST['login_email']) ? $_POST['login_email'] : "";
$password = isset($_POST['login_password']) ? $_POST['login_password'] : "";

$userO = new \models\user();
//$uID = "2";



if ($username && $password) {

	$uID = $userO->login($username, $password);
	if ($uID) {
		$app->reroute("/app/");
	}
}
$user = $userO->get($uID);



$app->set('user', $user);
$app->set('applications', array());




$app->route('GET /min/css/@filename', 'general->css_min');
$app->route('GET /min/css*', 'general->css_min');
$app->route('GET /min/js/@filename', 'general->js_min');
$app->route('GET /min/js*', 'general->js_min');


$app->route('GET|POST /login', 'controllers\controller_login->page');
$app->route('GET|POST /', 'controllers\controller_login->page');

$app->route('GET /screenshots', 'controllers\controller_screenshots->page');
$app->route('GET /news', 'controllers\controller_news->page');
$app->route('GET /news/@item', 'controllers\controller_news->page');
$app->route('GET /history', 'controllers\controller_history->page');
$app->route('GET /history/commits', 'controllers\controller_history->getCommits');
$app->route('GET /about', 'controllers\controller_about->page');
$app->route('GET /activity', 'controllers\controller_activity->page');
$app->route('GET /activity/data', 'controllers\controller_activity->data');


$app->route('GET|POST /app', function ($app) use ($user) {
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

$app->route('GET /app/keepalive', function ($app, $params) use ($user) {


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
		$t = array("ID" => $user['ID'], "idle" => $diff);

		test_array($t);

	}
);

$app->route('GET|POST /app/logout', function ($app, $params) use ($user) {
		session_unset();
		//session_destroy();
		$app->reroute("/login");
	}
);


$app->route('GET /ab/*', function($app){
		$url = "/app".$_SERVER['REQUEST_URI'];
		$app->reroute($url);
	});
$app->route('GET /ab', function($app){
		$url = "/app" . $_SERVER['REQUEST_URI'];
		$app->reroute($url);
	});



$router = array();
$apps = $app->get("applications");
foreach (glob("./apps/*/routes.php") as $route) {

	include_once($route);
	$app->set("applications", $apps);
}
//test_array($routes);

foreach ($router as $key=> $routes) {
	foreach ($routes as $route) {

		$app->route($route['method'] . ' ' . $route['path'], function () use ($app, $route, $key) {
				$str = array();
				$str[] = 'apps\\'.$key.'\\app->app';
				if ($route['a']) $str[] = 'apps\\' . $key . '\\app->access';
				if ($route['l']) $str[] = 'apps\\'.$key.'\\app->last_page';
				if ($route['controller']) $str[] = $route['controller'];
				$str = implode("; ", $str);
			//	test_array($str);
				$app->chain($str);
			}
		);

	}
	$app->route("GET|POST /app/$key/data/@function", function ($f3,$params) use ($app,$key) {
			$app->chain("apps\\$key\\app->app; apps\\$key\\app->access; apps\\$key\\controllers\\data\\data->" . $params['function']);
		}
	);
	$app->route("GET|POST /app/$key/data/@class/@function", function ($f3, $params) use ($app, $key) {

			$app->chain("apps\\$key\\app->app; apps\\$key\\app->access; apps\\$key\\controllers\\data\\" . $params['class'] . "->" . $params['function']);
		}
	);

	$app->route("GET|POST /app/$key/data/@folder/@class/@function", function ($f3, $params) use ($app, $key) {
			$app->chain("apps\\$key\\app->app; apps\\$key\\app->access; apps\\$key\\controllers\\data\\" . $params['folder'] . "\\" . $params['class'] . "->" . $params['function']);
		}
	);

	$app->route("GET|POST /app/$key/save/@function", function ($f3, $params) use ($app, $key) {
			$app->chain("apps\\$key\\app->app; apps\\$key\\app->access; apps\\$key\\controllers\\save\\save->" . $params['function']);
		}
	);
	$app->route("GET|POST /app/$key/save/@class/@function", function ($f3, $params) use ($app, $key) {
			$app->chain("apps\\$key\\app->app; apps\\$key\\app->access; apps\\$key\\controllers\\save\\" . $params['class'] . "->" . $params['function']);
		}
	);
	$app->route("GET|POST /app/$key/save/@folder/@class/@function", function ($f3, $params) use ($app, $key) {
			$app->chain("apps\\$key\\app->app; apps\\$key\\app->access; apps\\$key\\controllers\\save\\" . $params['folder'] . "\\" . $params['class'] . "->" . $params['function']);
		}
	);
	$app->route("GET /app/$key/access", function ($f3, $params) use ($app, $key) {
			$ap = "\\apps\\$key\\app";
			$a = new $ap();
			$user = $a->user();

			test_array($user);
			$applications = $app->get("applications");

			$tmpl = new \template("template.tmpl", "ui/front/", true);
			$tmpl->page = array(
				"section"    => "noaccess",
				"sub_section"=> "",
				"template"   => "page_no_access",
				"meta"       => array(
					"title"=> "No Access to the app",
				),

			);
			$tmpl->applications = $applications;
			$tmpl->application = $applications[$key];
			$tmpl->user = $user;
			$tmpl->output();

		}
	);

}


$app->route('GET|POST /app/@app/upload/', 'general->upload');

$app->route("GET|POST /app/@app/download/@folder/@ID/*", function ($f3, $params) use ($app, $key) {
		$a = $params['app'];
		$app->call("controllers\\$a\\controller_general_download->" . $params['folder']);
	}
);


$app->route("GET|POST /app/@app/thumb/@folder/@ID/*", function ($f3, $params) use ($app, $key) {
		$a = $params['app'];

		$app->call("controllers\\$a\\controller_general_thumb->" . $params['folder']);

		/*
		 $app->mutex(function () use ($folder, $app, $params) {
				$app->call("controllers\\$folder\\controller_general_thumb->" . $params['folder']);
			}
		);
		*/
	}
);
$app->route("GET|POST /app/@app/thumb/@folder/@ID", function ($f3, $params) use ($app, $key) {
		$a = $params['app'];
		$app->call("controllers\\$a\\controller_general_thumb->" . $params['folder']);
		/*
		$app->mutex(function () use ($folder, $app, $params) {
				$app->call("controllers\\$folder\\controller_general_thumb->" . $app->get('PARAMS.folder'));
			}
		);
		*/
	}
);



//test_array($app->routes);

$app->route('GET /php', function () {
		phpinfo();
		exit();
	}
);


$app->run();

