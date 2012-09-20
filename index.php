<?php


date_default_timezone_set('Africa/Johannesburg');
setlocale(LC_MONETARY, 'en_ZA');
ini_set('memory_limit', '256M');


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




$app->set('AUTOLOAD', './|lib/|lib/pChart/class/|controllers/|controllers/ab/|controllers/ab/data/|controllers/nf/|controllers/nf/data/');
$app->set('PLUGINS', 'lib/f3/|lib/suga/');
$app->set('CACHE', TRUE);
$app->set('DEBUG', 3);

$app->set('EXTEND', TRUE);
$app->set('UI', 'ui/');
$app->set('TEMP', 'temp/');

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


$version = '0.0.6';
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
$app->route('GET /screenshots', 'controllers\controller_screenshots->page');
$app->route('GET /history', 'controllers\controller_history->page');
$app->route('GET /history/commits', 'controllers\controller_history->getCommits');
$app->route('GET /about', 'controllers\controller_about->page');
$app->route('GET|POST /help', 'controllers\controller_docs->page');
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

// --------------------------------------------------------------------------------



function last_page(){
	$user = F3::get("user");
	F3::get("DB")->exec("UPDATE global_users SET last_page = '" . $_SERVER['REQUEST_URI'] . "' WHERE ID = '" . $user['ID'] . "'");

	$app = F3::get("app");
	$table = $app."_users_settings";
	F3::get("DB")->exec("UPDATE $table SET last_page = '" . $_SERVER['REQUEST_URI'] . "' WHERE uID = '" . $user['ID'] . "'");


	$st = array();
	$uID = $user['ID'];
	$cfg = F3::get("cfg");
	foreach ($cfg['apps'] as $a) {
		$st[] = "COALESCE((SELECT last_page FROM " . $a . "_users_settings WHERE uID = '$uID'),'/$a') as $a";
	}
	$st = implode(",", $st);
	$st = F3::get("DB")->exec("SELECT $st ");
	if (count($st))$st = $st[0];

	foreach ($cfg['apps'] as $a) {
		if (substr($st[$a],0,3)!="/$a"){
			$st[$a] = "/$a";
		}
	}

	F3::set("last_pages", $st);

	//test_array(F3::get("last_pages"));
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
$app->route('GET /ab/reports/marketer/targets', 'access; last_page; controllers\ab\controller_reports_marketer_targets->page');

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






// --------------------------------------------------------------------------------




$app->route('GET /nf', 'access; last_page; controllers\nf\controller_app_provisional->page');
$app->route('GET /nf/provisional', 'access; last_page; controllers\nf\controller_app_provisional->page');
$app->route('GET /nf/production', 'access; last_page; controllers\nf\controller_app_production->page');

$app->route('GET|POST /nf/test', function () use ($app) {
		$timer = new timer();
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

	$users = F3::get("DB")->sql("SELECT * FROM global_users");

		$ID = (isset($_GET['ID']))?$_GET['ID']:"";
		$new = (isset($_REQUEST['article']))? $_REQUEST['article']:"";
		$uID = (isset($_REQUEST['uID']))? $_REQUEST['uID']:"";
		$stage = (isset($_REQUEST['stage']))? $_REQUEST['stage']:"";
		$heading = (isset($_REQUEST['heading']))? $_REQUEST['heading']:"";
$postedCopy = "";
		$percent_t = "";
		$a = new Axon("test_articles");
		$a->load("ID='$ID'");

		$latest = getLatest($ID);
		$old = $latest['article'];



		if ($new!="" && $uID!="" && $stage!=""){

			if ($a->dry()) {
				$a->uID = $uID;
				$a->percent = 0.00;
				$a->heading = $heading;
				$a->stage = $stage;
				$a->save();
				$ID = $a->_id;
			}


			$b = new Axon("test_revisions");
			$b->load("aID='$ID' and stage='$stage'");

			$b->aID = $ID;
			$b->uID = $uID;
			$b->stage = $stage;
			$b->article = $new;
			$b->datein = date("Y-m-d H:i:s");
			$b->save();












				$patch ="";


				if ($old && ($old != $new)){
					$diff = s_text::diff($new, $old, "");
					$patch = $diff['patch'];
					F3::get("DB")->exec("INSERT INTO test_revisions_edits (aID,uID,patch) VALUES (:aID,:uID,:patch);", array(":aID"  => $ID,":uID"  => $uID, ":patch"=> $patch ));


					$postedCopy = getOriginal($ID);
					$percent_o = F3::scrub($postedCopy['article']);
					$percent_a = F3::scrub($new);
					$percent_t = s_text::diff($percent_o, $percent_a, " ");

					$a->percent = $percent_t['stats']['percent'];
					$a->save();


				}

			//


				$app->reroute("/nf/test/?ID=$ID");





		}

		if ($postedCopy==""){
			$postedCopy = getOriginal($ID);
		}

		if ($percent_t==""){
			$percent_o = F3::scrub($postedCopy['article']);
			$percent_a = F3::scrub($old);
			$percent_t = s_text::diff($percent_o, $percent_a, " ");
		}









		echo '<style>';
		echo 'body { font-size: 12px; }';
		echo 'h1, h2, h3, h4, h5 { background-color:  rgba(0, 0, 0, 0.2); }';
		echo 'del { background-color:  rgba(255, 0, 0, 0.2);}';
		echo 'ins { background-color: rgba(0, 128, 0, 0.3);}';
		echo 'li { border-bottom: 1px dotted #ccc; margin-bottom: 5px;  padding-bottom: 10px; }';
		echo 'th { text-align: left; }';
		echo 'th, td { border-right: 1px dotted #ccc; vertical-align: top; border-bottom: 1px dotted #ccc;}';
		echo '#article { width: 100%; height: 200px; }';
		echo '.s { font-size: 10px; }';
		echo '</style>';

		$records = F3::get("DB")->sql("SELECT *, (SELECT fullName FROM global_users WHERE global_users.ID = test_articles.uID ) as fullName FROM test_articles");

		if ($a->dry()){
			echo '<table width="100%"><tr><th>Heading</th><th>Author</th><th>Percent</th></tr>';
			foreach ($records as $record) {
				echo '<tr>';
				echo '<td><a href="?ID=' . $record['ID'] . '">';
				echo $record['heading'];
				echo '</a></td>';
				echo '<td>'.$record['fullName'].'</td>';
				echo '<td>'.$record['percent'].'%</td>';
				echo '</tr>';
			}
			echo '</table>';
		} else {
			echo '<a href="?ID=">Back</a>';

			echo '<h1>' . $a->heading . '</h1><fieldset><legend>Latest version fo the article</legend>';
			echo $old;
			echo '</fieldset>';
			echo '<small>by '.$latest['authorName'].'</small><br>';
			echo '<small>Last change by '.$latest['fullName'].'</small><br>';
			echo '<small>Total Percent Change '. $percent_t['stats']['percent'].'%</small>';

			$edits = getChain($ID);

			echo '<h3>Origional</h3><fieldset><legend>The sbmitted copy (latest under drafts)</legend>';
			echo $postedCopy['article'];



			echo '</fieldset><p>&nbsp;</p><h3>Stages <small>- The 4 stages an article goes through showing changes to the posted copy</small> </h3>';
			$stages = F3::get("DB")->exec("SELECT * FROM test_revisions WHERE aID = '$ID' ORDER BY stage ASC");
			echo '<table style="width: 100%;"><tr><th width=20%>Stage</th><th width=80%>changes</th></tr>';


			$laststage = $postedCopy['article'];
			foreach ($stages as $stage) {

				$diff = percentDiff($laststage, $stage['article'], true);
				$laststage = $stage['article'];
				echo '<tr><td>Stage - ';

				switch ($stage['stage']){
					case '1':
						echo "Draft";
						break;
					case '2':
						echo "sub-edit";
						break;
					case '3':
						echo "proof";
						break;
					case '4':
						echo "ready";
						break;
				}
				//echo $stage['stage'];
				echo ' - [ +' . $diff['stats']['added'] . "  -" . $diff['stats']['removed'] . " ] &nbsp; &nbsp;" . $diff['stats']['percent'] . '% ';
				echo '</td><td class="s">';
				echo $diff['html'];
				echo '</td><td>';

				echo '</td></tr>';

			}
			echo '</table>';
			echo '<h3>Edits <small>- every time the article was changed. this shows those changes</small></h3>';

			echo '<table style="width: 100%;"><tr><th width=15%>Details</th><th width=28%>Was</th><th width=28%>became</th><th width=28%>changes</th></tr>';



			foreach ($edits['edits'] as $edit) {
				

				echo '<tr><td>';
				echo '[ +' . $edit['stats']['added'] . "  -" . $edit['stats']['removed'] . " ] &nbsp; &nbsp;" . $edit['stats']['percent'] . '% <br>'. $edit['fullName'] . "<br><span class='s'>" . $edit['datein']."</span>";
				echo '</td><td class="s">';

				echo $edit['was'];
				echo '</td><td class="s">';
				echo $edit['became'];
				echo '</td><td class="s">';
				echo $edit['html'];
				echo '</td></tr>';


			}



			echo '</table>';


		}

		echo '<p>&nbsp; </p>';
		echo 'timer: '. $timer->stop();;

echo "<p>&nbsp; </p><hr><p>&nbsp; </p>";


		echo "<form action='/nf/test?ID=$ID' method='post'>";

		echo '<input type="text" name="heading" id="heading" style="width: 100%;" placeholder="Heading" value="'.$a->heading.'" /> ';

		echo "<textarea id='article' name='article' placeholder='Article'>$old</textarea>";

		echo "Stages: | ";
		$selected = "";
		if ($latest['stage']=='1'){
			$selected = "checked='checked'";
		}
		echo '<label> <input type="radio" name="stage" id="stage_1" ' . $selected . ' value="1"> Draft </label>| ';
		if (!$a->dry()){
			$selected = "";
			if ($latest['stage'] == '2') {
				$selected = "checked='checked'";
			}
			echo '<label> <input type="radio" name="stage" ' . $selected . ' id="stage_2" value="2"> sub </label>| ';
			$selected = "";
			if ($latest['stage'] == '3') {
				$selected = "checked='checked'";
			}
			echo '<label> <input type="radio" name="stage" ' . $selected . ' id="stage_3" value="3"> proof </label>| ';
			$selected = "";
			if ($latest['stage']== '4') {
				$selected = "checked='checked'";
			}
			echo '<label> <input type="radio" name="stage" '.$selected.' id="stage_4" value="4"> ready </label>| ';
		}


		echo '&nbsp;&nbsp;&nbsp;&nbsp;<select id="uID" name="uID"> ';
		foreach ($users as $user){

			$selected = "";
			if ($user['ID']== $latest['uID']){
				$selected = "selected='selected'";
			}

			echo '<option value="'.$user['ID'].'" '.$selected.'>' . $user['fullName'] . '</option>';
		}
		echo '</select>';
		echo "&nbsp;&nbsp;&nbsp;&nbsp;<button type='submit'>save</button> ";
		echo "</form>";

		exit();
});
function getOriginal($ID){
	$latest = F3::get("DB")->exec("SELECT *, (SELECT fullName FROM global_users WHERE global_users.ID =test_revisions.uID ) as fullName, (SELECT fullName FROM global_users WHERE global_users.ID =(SELECT uID FROM test_articles WHERE test_articles.ID = '$ID') ) as authorName FROM test_revisions WHERE aID = '$ID' AND stage = '1' ORDER BY datein DESC");
	$return = array("ID"      => "",
	                "article" => "",
	                'uID'     => "",
	                'fullName'=> "",
	                'stage'   => "1"
	);
	if (count($latest)) {
		$return = $latest[0];
	}
	return $return;
}
function getLatest($ID){
	$latest = F3::get("DB")->exec("SELECT *, (SELECT fullName FROM global_users WHERE global_users.ID =test_revisions.uID ) as fullName, (SELECT fullName FROM global_users WHERE global_users.ID =(SELECT uID FROM test_articles WHERE test_articles.ID = '$ID') ) as authorName FROM test_revisions WHERE aID = '$ID' ORDER BY datein DESC");
	$return = array("ID"=>"","article"=>"",'uID'=>"",'fullName'=>"",'stage'=>"1");
	if (count($latest)){
		$return = $latest[0];
	}
	return $return;
}
function getChain($ID){
	$latest = getLatest($ID);
	$article = $latest['article'];

	$edits = F3::get("DB")->exec("SELECT *, (SELECT fullName FROM global_users WHERE global_users.ID =test_revisions_edits.uID ) as fullName FROM test_revisions_edits WHERE aID = '$ID' ORDER BY datein DESC");

	$return = array(
		"latest"=> $latest,
		"origional"=>"",
		"edits"=> array()
	);
	$was = $article;


	foreach ($edits as $revision) {
		$became = $was;
		$was = s_text::patch($became, $revision['patch'], false);

		$dif = percentDiff($was, $became, true);

		$return['edits'][] = array(
			"datein"   => $revision['datein'],
			"fullName"   => $revision['fullName'],
			"was"   => $was,
			"became"=> $became,
			"patch" => htmlentities($revision['patch']),
			"html"  => $dif['html'],
			"stats" => array(
				"percent"=> $dif['stats']['percent'],
				"added"  => $dif['stats']['added'],
				"removed"=> $dif['stats']['removed']
			)
		);



	}


	return $return;



}



$app->route('GET|POST /nf/import12345', function () use ($app) {

		if (isLocal()){


			/*
		 of: 0
		 show
		 */

			$records_show = 30;
			$offset = isset($_REQUEST['offset'])? $_REQUEST['offset']:0;
			$newoffset = $offset + 1;

			$start_offset = $offset * $records_show;





			$DB = new DB('mysql:host=localhost;dbname=apps', 'william', 'stars');
			//$DB->sql("TRUNCATE TABLE apps.nf_articles_revisions");

			echo '<style>';
			echo 'body { font-size: 13px; }';
			echo 'del { background-color:  rgba(255, 0, 0, 0.2);}';
			echo 'ins { background-color: rgba(0, 128, 0, 0.3);}';
			echo '</style>';

			$records = $DB->sql("SELECT * FROM apps.nf_articles ORDER BY ID ASC LIMIT $start_offset,$records_show");
			echo "showing: (".count($records).") | $start_offset,$records_show<hr> ";
			if (!count($records)) {
				echo "DONE";
				exit();
			};

			foreach ($records as $record){



				$ID = $record['ID'];
				$o = $record['article_orig'];
				$a = $record['article'];
				$s = $record['synopsis'];
				$uID = $record['authorID'];
				$lb = $record['lockedBy'];
				$d = $record['datein'];
				echo "<article>" . $record['ID'] . " | " . $record['heading'] . "<p>";


				$filesID = "";
				$files = $DB->sql("SELECT * FROM apps.nf_files WHERE aID = '$ID' ORDER BY ID ASC");
				if (count($files)) {
					$pf = array();
					foreach ($files as $file) $pf[] = $file['ID'];

					$filesID = implode(",", $pf);
				}


				$percent = 0.00;
				$patch = "";



				if ($o && $a){
					$timer = new timer();
					$diff = percentDiff($o, $a, true);
					$t = $timer->stop($record['ID']);

					echo "added: " . ($diff['stats']['added']) . " | removed: " . ($diff['stats']['removed']) . " | old: " . $diff['stats']['old'] . " | new: " . $diff['stats']['new'] . " | percent: " . $diff['stats']['percent']." | time: ".$t. "<br>";


					$percent = $diff['stats']['percent'];

					$patch = $diff['patch'];



					$DB->exec("UPDATE apps.nf_articles SET percent = '". $percent ."' WHERE ID = '".$ID."'");
				}
				echo $filesID;
				echo "</article><hr>";

				if ($o){

					$DB->exec("INSERT INTO nf_articles_revisions (aID,uID,remark,synopsis,article, patch, filesID, datein) VALUES (:aID,:uID,'New Article - import',:synopsis,:article, :patch, :filesID, :datein);",array( ":aID"=>$ID,":uID"=>$uID,":synopsis"=>$s,":article"=>$o,":patch"=>"",":filesID"=>$filesID, ":datein"=> $d));
				}
				if ($a){
					$DB->exec("INSERT INTO nf_articles_revisions (aID,uID,remark,synopsis,article, patch, filesID, percent,  datein) VALUES (:aID,:uID,'Edit Article - import',:synopsis,:article, :patch, :filesID, :percent, :datein);",array( ":aID"=>$ID,":uID"=> $lb,":synopsis"=>$s,":article"=>$a,":patch"=>$patch,":filesID"=>$filesID,":percent"=>$percent, ":datein"=> $d));
				}


			}

			if (count($records)){
				//echo "redirect to " . $newoffset;

				echo "<meta http-equiv='refresh' content='1;URL=?offset=" . $newoffset."&r=".date("YmdHis")."'>";
				/*exit();
			 sleep(5);
			 $app->reroute("?offset=".$newoffset);*/
			}

			exit();
		}

});






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
	$notificationmodel = "\\models\\$folder\\user_notifications";
	$GLOBALS["output"]['notifications'] = $notificationmodel::show();
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









