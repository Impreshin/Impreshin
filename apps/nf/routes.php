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
// administration

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/admin/dates',
	"controller"=>'apps\nf\controllers\admin_dates->page',
	"a"=>true,
	"l"=>true,
);

$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/admin/users',
	"controller"=>'apps\nf\controllers\admin_users->page',
	"a"=>true,
	"l"=>true,
);


$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/nf/admin/publications',
	"controller"=>'apps\nf\controllers\admin_publications->page',
	"a"=>true,
	"l"=>true,
);






// utilities





$router['nf'] = $routes;

$apps["nf"] = array(
		"name" => "NewsFiler",
		"description"=>"Editorial content management tool"
);



?>
