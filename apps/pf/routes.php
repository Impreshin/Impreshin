<?php

$routes = array();

// main app
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/pf',
	"controller"=>'apps\pf\controllers\front->page',
	"a"=>true,
	"l"=>true,
);
// administration






$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/pf/admin/dates',
	"controller"=>'apps\pf\controllers\admin\dates->page',
	"a"=>true,
	"l"=>true,
);




$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/pf/admin/publications',
	"controller"=>'apps\pf\controllers\admin\publications->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/pf/admin/users',
	"controller"=>'apps\pf\controllers\admin\users->page',
	"a"=>true,
	"l"=>true,
);

$app->route("GET|POST /app/np/thumb", function ($app, $params) {
		$app->chain("apps\\pf\\app->app; apps\\pf\\controllers\\_file->thumbnail");

	}
);

$app->route("GET|POST /app/pf/thumb/@w/@h", function ($app, $params) {
		$app->chain("apps\\pf\\app->app; apps\\pf\\controllers\\_file->thumbnail");

	}
);
$app->route("GET|POST /app/pf/download", function ($app, $params) {
		$app->chain("apps\\pf\\app->app; apps\\pf\\controllers\\_file->download");

	}
);
$app->route("GET|POST /app/pf/thumb/page/@dID/@page/*", function ($app, $params) {

		$app->chain("apps\\ab\\app->app; apps\\ab\\controllers\\controller_general_thumb->page");

	}
);

// utilities





$router['pf'] = $routes;

$apps["pf"] = array(
		"name" => "Pages",
		"description"=>"Upload full page PDF"
);



?>
