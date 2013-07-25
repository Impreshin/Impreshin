<?php
/**
 * User: William
 * Date: 2013/07/17 - 11:55 AM
 */
namespace core;
class app {
	function __construct() {

		$this->f3 = require('lib/f3/base.php');
		require_once('inc/class.timer.php');
		$this->pageExecute = new \timer(true);
		ob_start();
		$this->routes = array();
	}

	function __destruct() {

	}

	function set($key, $val, $ttl = 0) {
		return $this->f3->set($key, $val, $ttl);
	}

	function get($key) {
		return $this->f3->get($key);
	}

	function route($pattern, $handler, $ttl = 0, $kbps = 0) {
		$this->routes[] = array($pattern, $handler, $ttl, $kbps);
		return $this->f3->route($pattern, $handler, $ttl, $kbps);
	}

	function reroute($uri) {
		return $this->f3->reroute($uri);
	}

	function chain($funcs, $args = NULL) {
		return $this->f3->chain($funcs,$args);
	}

	function run() {

		$cfg = $this->f3->get("CFG");
		$app = $this->f3->get("app");
		$user = $this->f3->get("user");

		$this->f3->run();

		$this->f3->get("DB")->exec("UPDATE global_users SET last_app = '$app', last_activity = now() WHERE ID = '" . $user['ID'] . "'");

		if ($app) {
			$app->get("DB")->exec("UPDATE " . $app . "_users_settings SET  last_activity = now() WHERE uID = '" . $user['ID'] . "'");
		}


		$GLOBALS["render"] = ob_get_contents();
		$pageSize = ob_get_length();
		ob_end_clean();
		$t = array();

		// build up the models timers for debugging purposes

		$models = $GLOBALS['models'];
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

		$totaltime = $this->pageExecute->stop("Page Execute");
		$GLOBALS["output"]['timer'] = $GLOBALS['timer'];
		$GLOBALS["output"]['models'] = $models;
		$GLOBALS["output"]['page'] = array("page" => $_SERVER['REQUEST_URI'], "time" => $totaltime, "size" => ($pageSize));

	//	$models = $GLOBALS['models'];


		ob_start("ob_gzhandler");
		if ($this->get("json")){ // if json is set then to render the json data instead of outputting a whole page

			header("Content-Type: application/json");
			echo json_encode($GLOBALS["output"]);
		} else {
			$timersbottom = '
					<script type="text/javascript">
						if (typeof(updatetimerlist) != "undefined") {
				            updatetimerlist(' . json_encode($GLOBALS["output"]) . ');
				        }
					</script>
				';

			$render = sanitize_output($GLOBALS["render"]);

			if (strpos($GLOBALS["render"], "<!--print version-->") || strpos($GLOBALS["render"], "<!--no_timer_list-->")) {
				echo $render;
			} else {
				echo str_replace("</body>", $timersbottom . '</body>', $render);
			}
		}

	}



}
