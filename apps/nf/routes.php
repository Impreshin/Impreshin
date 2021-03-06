<?php

$routes = array();

// main app
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf',
	"controller"=>'apps\nf\controllers\provisional->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/provisional/print',
	"controller"=>'apps\nf\controllers\provisional->_print',
	"a"=>true,
	"l"=>false,
);

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/newsbook',
	"controller"=>'apps\nf\controllers\newsbook->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/newsbook/print',
	"controller"=>'apps\nf\controllers\newsbook->_print',
	"a"=>true,
	"l"=>false,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/layout',
	"controller"=>'apps\nf\controllers\layout->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/production',
	"controller"=>'apps\nf\controllers\production->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/form',
	"controller"=>'apps\nf\controllers\form->page_new',
	"a"=>true,
	"l"=>true,
);

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/form/@ID',
	"controller"=>'apps\nf\controllers\form->page_edit',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/records/search',
	"controller"=>'apps\nf\controllers\search->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/records/search/print',
	"controller"=>'apps\nf\controllers\search->_print',
	"a"=>true,
	"l"=>false,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/records/newsbook',
	"controller"=>'apps\nf\controllers\records_newsbook->page',
	"a"=>true,
	"l"=>true,
);

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/records/deleted',
	"controller"=>'apps\nf\controllers\deleted->page',
	"a"=>true,
	"l"=>true,
);

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/records/deleted/print',
	"controller"=>'apps\nf\controllers\deleted->_print',
	"a"=>true,
	"l"=>false,
);

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/records/archives',
	"controller"=>'apps\nf\controllers\records_archives->page',
	"a"=>true,
	"l"=>true,
);


// reports
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/reports/author/submitted',
	"controller"=>'apps\nf\controllers\reports\author_submitted->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/reports/author/newsbook',
	"controller"=>'apps\nf\controllers\reports\author_newsbook->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/reports/publication/figures',
	"controller"=>'apps\nf\controllers\reports\publication_figures->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/reports/category/figures',
	"controller"=>'apps\nf\controllers\reports\category_figures->page',
	"a"=>true,
	"l"=>true,
);
// administration

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/admin/checklists',
	"controller"=>'apps\nf\controllers\admin\checklists->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/admin/priorities',
	"controller"=>'apps\nf\controllers\admin\priorities->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/admin/categories',
	"controller"=>'apps\nf\controllers\admin\categories->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/admin/stages',
	"controller"=>'apps\nf\controllers\admin\stages->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/admin/sections',
	"controller"=>'apps\nf\controllers\admin\sections->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET|POST',
	"path"=>'/app/nf/admin/stylesheet',
	"controller"=>'apps\nf\controllers\admin\cmstylesheet->page',
	"a"=>true,
	"l"=>true,
);

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/admin/loading',
	"controller"=>'apps\nf\controllers\admin\loading->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/admin/resources',
	"controller"=>'apps\nf\controllers\admin\resources->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET|POST',
	"path"=>'/app/nf/admin/dictionary',
	"controller"=>'apps\nf\controllers\admin\dictionary->page',
	"a"=>true,
	"l"=>true,
);





$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/admin/dates',
	"controller"=>'apps\nf\controllers\admin\dates->page',
	"a"=>true,
	"l"=>true,
);




$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/admin/publications',
	"controller"=>'apps\nf\controllers\admin\publications->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/admin/users',
	"controller"=>'apps\nf\controllers\admin\users->page',
	"a"=>true,
	"l"=>true,
);

$app->route("GET|POST /app/nf/thumb", function ($app, $params) {
		$app->chain("apps\\nf\\app->app; apps\\nf\\controllers\\_file->thumbnail");

	}
);

$app->route("GET|POST /app/nf/thumb/@w/@h", function ($app, $params) {
		$app->chain("apps\\nf\\app->app; apps\\nf\\controllers\\_file->thumbnail");

	}
);
$app->route("GET|POST /app/nf/download", function ($app, $params) {
		$app->chain("apps\\nf\\app->app; apps\\nf\\controllers\\_file->download");

	}
);
$app->route("GET|POST /app/nf/thumb/page/@dID/@page/*", function ($app, $params) {

		$app->chain("apps\\ab\\app->app; apps\\ab\\controllers\\controller_general_thumb->page");

	}
);

// utilities
foreach (glob("./apps/nf/share/*", GLOB_ONLYDIR) as $folder) {
	
	$folder = trim($folder,"./");
	$file = str_replace('apps/nf/share/','',$folder);
	//test_array($file); 
	include_once("{$folder}/{$file}.php");
	$route_list = \apps\nf\share\send_to_lin::getInstance()->routes();
	
	foreach ($route_list as $item){
		$routes[] = $item;
	}
	
}







$router['nf'] = $routes;
if (in_array("nf",$cfg['apps'])) {
	$apps["nf"] = array(
			"name" => "NewsFiler",
			"description" => "Editorial Content Management Tool"
	);
};


?>
