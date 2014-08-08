<?php

$routes = array();

// main app
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab',
	"controller"=>'apps\ab\controllers\controller_app_provisional->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/provisional/print',
	"controller"=>'apps\ab\controllers\controller_app_provisional->_print',
	"a"=>true,
	"l"=>false,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/print/details',
	"controller"=>'apps\ab\controllers\controller_app_details->_print',
	"a"=>true,
	"l"=>false,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/production',
	"controller"=>'apps\ab\controllers\controller_app_production->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/production/print',
	"controller"=>'apps\ab\controllers\controller_app_production->_print',
	"a"=>true,
	"l"=>false,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/layout',
	"controller"=>'apps\ab\controllers\controller_app_layout->page',
	"a"=>true,
	"l"=>true,
);

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/overview',
	"controller"=>'apps\ab\controllers\controller_app_overview->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/records/search',
	"controller"=>'apps\ab\controllers\controller_app_search->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/records/search/print',
	"controller"=>'apps\ab\controllers\controller_app_search->_print',
	"a"=>true,
	"l"=>false,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/records/deleted',
	"controller"=>'apps\ab\controllers\controller_app_deleted->page',
	"a"=>true,
	"l"=>true,
);

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/records/deleted/print',
	"controller"=>'apps\ab\controllers\controller_app_deleted->_print',
	"a"=>true,
	"l"=>false,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/form',
	"controller"=>'apps\ab\controllers\controller_app_form->page_new',
	"a"=>true,
	"l"=>true,
);

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/form/@ID',
	"controller"=>'apps\ab\controllers\controller_app_form->page_edit',
	"a"=>true,
	"l"=>true,
);

// reports

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/reports/publication/figures',
	"controller"=>'apps\ab\controllers\controller_reports_publication_figures->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/reports/publication/discounts',
	"controller"=>'apps\ab\controllers\controller_reports_publication_discounts->page',
	"a"=>true,
	"l"=>true,
);

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/reports/publication/section',
	"controller"=>'apps\ab\controllers\controller_reports_publication_section_figures->page',
	"a"=>true,
	"l"=>true,
);


$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/reports/publication/placing',
	"controller"=>'apps\ab\controllers\controller_reports_publication_placing_figures->page',
	"a"=>true,
	"l"=>true,
);


$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/reports/account/figures',
	"controller"=>'apps\ab\controllers\controller_reports_account_figures->page',
	"a"=>true,
	"l"=>true,
);


$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/reports/account/discounts',
	"controller"=>'apps\ab\controllers\controller_reports_account_discounts->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/reports/marketer/figures',
	"controller"=>'apps\ab\controllers\controller_reports_marketer_figures->page',
	"a"=>true,
	"l"=>true,
);


$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/reports/marketer/discounts',
	"controller"=>'apps\ab\controllers\controller_reports_marketer_discounts->page',
	"a"=>true,
	"l"=>true,
);

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/reports/marketer/targets',
	"controller"=>'apps\ab\controllers\controller_reports_marketer_targets->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/reports/production/figures',
	"controller"=>'apps\ab\controllers\controller_reports_production_figures->page',
	"a"=>true,
	"l"=>true,
);

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/reports/production/figures',
	"controller"=>'apps\ab\controllers\controller_reports_production_figures->page',
	"a"=>true,
	"l"=>true,
);

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/reports/category/figures',
	"controller"=>'apps\ab\controllers\controller_reports_category_figures->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/reports/category/discounts',
	"controller"=>'apps\ab\controllers\controller_reports_category_discounts->page',
	"a"=>true,
	"l"=>true,
);
// administration

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/admin/dates',
	"controller"=>'apps\ab\controllers\controller_admin_dates->page',
	"a"=>true,
	"l"=>true,
);

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/admin/users',
	"controller"=>'apps\ab\controllers\controller_admin_users->page',
	"a"=>true,
	"l"=>true,
);

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/admin/accounts',
	"controller"=>'apps\ab\controllers\controller_admin_accounts->page',
	"a"=>true,
	"l"=>true,
);

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/admin/accounts/status',
	"controller"=>'apps\ab\controllers\controller_admin_accounts_status->page',
	"a"=>true,
	"l"=>true,
);

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/admin/accounts/import',
	"controller"=>'apps\ab\controllers\controller_admin_accounts_import->page',
	"a"=>true,
	"l"=>true,
);

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/admin/sections',
	"controller"=>'apps\ab\controllers\controller_admin_sections->page',
	"a"=>true,
	"l"=>true,
);

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/admin/categories',
	"controller"=>'apps\ab\controllers\controller_admin_categories->page',
	"a"=>true,
	"l"=>true,
);

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/admin/marketers',
	"controller"=>'apps\ab\controllers\controller_admin_marketers->page',
	"a"=>true,
	"l"=>true,
);

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/admin/marketers/targets',
	"controller"=>'apps\ab\controllers\controller_admin_marketers_targets->page',
	"a"=>true,
	"l"=>true,
);

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/admin/production',
	"controller"=>'apps\ab\controllers\controller_admin_production->page',
	"a"=>true,
	"l"=>true,
);

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/admin/placing',
	"controller"=>'apps\ab\controllers\controller_admin_placing->page',
	"a"=>true,
	"l"=>true,
);

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/admin/placing/colours',
	"controller"=>'apps\ab\controllers\controller_admin_placing_colours->page',
	"a"=>true,
	"l"=>true,
);

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/admin/loading',
	"controller"=>'apps\ab\controllers\controller_admin_loading->page',
	"a"=>true,
	"l"=>true,
);

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/admin/inserts_types',
	"controller"=>'apps\ab\controllers\controller_admin_inserts_types->page',
	"a"=>true,
	"l"=>true,
);

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/admin/publications',
	"controller"=>'apps\ab\controllers\controller_admin_publications->page',
	"a"=>true,
	"l"=>true,
);

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/ab/admin/company',
	"controller"=>'apps\ab\controllers\controller_admin_company->page',
	"a"=>true,
	"l"=>true,
);






// utilities





$router['ab'] = $routes;

$apps["ab"] = array(
		"name" => "AdBooker",
		"description"=>"Advert management tool"
);




$app->route("GET|POST /app/ab/download/@folder/@ID/*", function ($app, $params)  {
		$app->chain("apps\\ab\\app->app; apps\\ab\\controllers\\controller_general_download->" . $params['folder']);
	}
);

$app->route("GET|POST /app/ab/thumb/@folder/@ID/*", function ($app, $params) {

		$app->chain("apps\\ab\\app->app; apps\\ab\\controllers\\controller_general_thumb->" . $params['folder']);

	}
);
$app->route("GET|POST /app/ab/thumb/@folder/@ID", function ($app, $params) {

		$app->chain("apps\\ab\\app->app; apps\\ab\\controllers\\controller_general_thumb->" . $params['folder']);

	}
);
$app->route("GET|POST /app/ab/logs/@function", function ($app, $params) {
		$app->chain("apps\\ab\\app->app; apps\\ab\\controllers\\data\\logging->getSection");



	}
);

$app->route("GET|POST /app/ab/thumb/page/@dID/@page/*", function ($app, $params) {

		$app->chain("apps\\ab\\app->app; apps\\ab\\controllers\\controller_general_thumb->page");

	}
);


/*




$app->route('GET /app/ab/form', function ($f3, $params) use ($user) {
		//test_array($user);
		if ($user['permissions']['form']['new']) {
			$f3->chain('apps\app->access; apps\ab\app->app; apps\ab\app->last_page;  apps\ab\controllers\controller_app_form->page');
		} else {
			$f3->error(404);
		}

	}
);
$app->route('GET /app/ab/form/@ID', function ($f3, $params) use ($user) {
		if ($user['permissions']['form']['edit'] || $user['permissions']['form']['edit_master'] || $user['permissions']['form']['delete']) {
			$f3->chain('apps\app->access; apps\ab\app->app; apps\ab\app->last_page;  apps\ab\controllers\controller_app_form->page');
		} else {
			$f3->error(404);

		}
	}
);

$app->route('GET /app/ab/admin/dates', function ($f3, $params) {
		$f3->chain('apps\app->access;  last_page;  controllers\ab\controller_admin_dates->page');
	}
);
$app->route('GET /app/ab/admin/users', function ($f3, $params) {
		$f3->chain('apps\app->access;  last_page; controllers\ab\controller_admin_users->page');
	}
);
$app->route('GET /app/ab/admin/accounts', function ($f3, $params) {
		$f3->chain('apps\app->access;  last_page; controllers\ab\controller_admin_accounts->page');
	}
);
$app->route('GET /app/ab/admin/accounts/status', function ($f3, $params) {
		$f3->chain('apps\app->access;  last_page; controllers\ab\controller_admin_accounts_status->page');
	}
);
$app->route('GET /app/ab/admin/accounts/import', function ($f3, $params) {
		$f3->chain('apps\app->access;  last_page; controllers\ab\controller_admin_accounts_import->page');
	}
);
$app->route('GET /app/ab/admin/sections', function ($f3, $params) {
		$f3->chain('apps\app->access;  last_page; controllers\ab\controller_admin_sections->page');
	}
);
$app->route('GET /app/ab/admin/categories', function ($f3, $params) {
		$f3->chain('apps\app->access;  last_page; controllers\ab\controller_admin_categories->page');
	}
);
$app->route('GET /app/ab/admin/marketers', function ($f3, $params) {
		$f3->chain('apps\app->access;  last_page; controllers\ab\controller_admin_marketers->page');
	}
);
$app->route('GET /app/ab/admin/marketers/targets', function ($f3, $params) {
		$f3->chain('apps\app->access;  last_page; controllers\ab\controller_admin_marketers_targets->page');
	}
);
$app->route('GET /app/ab/admin/production', function ($f3, $params) {
		$f3->chain('apps\app->access;  last_page; controllers\ab\controller_admin_production->page');
	}
);
$app->route('GET /app/ab/admin/placing', function ($f3, $params) {
		$f3->chain('apps\app->access;  last_page; controllers\ab\controller_admin_placing->page');
	}
);
$app->route('GET /app/ab/admin/placing/colours', function ($f3, $params) {
		$f3->chain('apps\app->access;  last_page; controllers\ab\controller_admin_placing_colours->page');
	}
);
$app->route('GET /app/ab/admin/loading', function ($f3, $params) {
		$f3->chain('apps\app->access;  last_page; controllers\ab\controller_admin_loading->page');
	}
);
$app->route('GET /app/ab/admin/inserts_types', function ($f3, $params) {
		$f3->chain('apps\app->access;  last_page; controllers\ab\controller_admin_inserts_types->page');
	}
);

$app->route('GET /app/ab/admin/publications', function ($f3, $params) {
		$f3->chain('apps\app->access;  last_page; controllers\ab\controller_admin_publications->page');
	}
);

// --------------------------------------------------------------------------------


*/

// --------------------------------------------------------------------------------

?>
