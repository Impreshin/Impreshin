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
	"path"=>'/app/nf/newsbook',
	"controller"=>'apps\nf\controllers\newsbook->page',
	"a"=>true,
	"l"=>true,
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
// reports
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/reports/author/submitted',
	"controller"=>'apps\nf\controllers\reports\author_submitted->page',
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
	"path"=>'/app/nf/admin/cmstylesheet',
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


// utilities





$router['nf'] = $routes;

$apps["nf"] = array(
		"name" => "NewsFiler",
		"description"=>"Editorial content management tool"
);



?>
